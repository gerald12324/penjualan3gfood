<?php

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\MenuItem;
use App\Models\Courier;
use App\Models\CourierPayout;

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', function () {
        return view('login');
    })->name('login');

    Route::post('/admin/login', function (Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('/admin');
        }

        return back()->withErrors([
            'email' => 'Email atau password tidak sesuai.',
        ])->onlyInput('email');
    })->name('admin.login.submit');
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->middleware('auth')->name('logout');

Route::any('/', function () {
    $screen = request()->query('screen', 'home');
    $category = request()->query('category');
    $sessionOrder = session('order');
    $order = $sessionOrder && isset($sessionOrder['order_code'])
        ? Order::where('order_code', $sessionOrder['order_code'])->first()?->toArray()
        : null;

    return view('foodie', [
        'screen' => in_array($screen, ['home', 'cart', 'buyer', 'payment', 'confirmation', 'status', 'feedback'], true)
            ? $screen
            : 'home',
        'order' => $order,
        'category' => $category,
        'managedMenu' => MenuItem::where('is_available', true)->orderBy('category')->orderBy('name')->get(),
    ]);
});

Route::get('/cart/add/{item}', function (int $item) {
    $cart = session('cart', []);
    $cart[$item] = ($cart[$item] ?? 0) + 1;
    session(['cart' => $cart]);
    return redirect()->to('/?screen=home');
})->whereNumber('item');

Route::get('/cart/remove/{item}', function (int $item) {
    $cart = session('cart', []);
    unset($cart[$item]);
    session(['cart' => $cart]);

    return redirect()->to('/?screen=cart');
})->whereNumber('item');

Route::get('/cart/clear', function () {
    session()->forget('cart');

    return redirect()->to('/?screen=cart');
});

Route::post('/checkout/buyer', function (Request $request) {
    $data = $request->validate([
        'customer_name' => ['required', 'string', 'max:120'],
        'phone' => ['required', 'string', 'max:30'],
        'address' => ['required', 'string', 'max:500'],
    ]);
    session(['buyer' => $data]);
    return redirect()->to('/?screen=payment');
})->name('checkout.buyer');

Route::post('/checkout/payment', function (Request $request) {
    $method = $request->validate(['payment_method' => ['required', 'in:cash,bank,digital']])['payment_method'];
    $buyer = session('buyer');
    abort_unless($buyer, 302, 'Data pembeli belum diisi.');
    $subtotal = 0;
    foreach (session('cart', []) as $itemId => $quantity) {
        $menuItem = MenuItem::where('id', $itemId)->where('is_available', true)->first();
        $subtotal += ($menuItem?->price ?? 0) * $quantity;
    }
    abort_unless($subtotal > 0, 302, 'Keranjang masih kosong.');
    $order = Order::create([
        ...$buyer,
        'order_code' => 'FE-' . now()->format('ymdHis'),
        'payment_method' => $method,
        'total' => $subtotal + 1000,
        'status' => 'waiting',
    ]);
    foreach (session('cart', []) as $itemId => $quantity) {
        $menuItem = MenuItem::where('id', $itemId)->where('is_available', true)->first();
        if ($menuItem) {
            $order->items()->create([
                'menu_item_id' => $menuItem->id,
                'menu_name' => $menuItem->name,
                'unit_price' => $menuItem->price,
                'quantity' => $quantity,
            ]);
        }
    }
    session(['order' => $order->fresh()->toArray()]);
    session()->forget('cart');
    return redirect()->to('/?screen=confirmation');
})->name('checkout.payment');

Route::middleware('auth')->get('/admin', function () {
    $status = request()->query('status');
    $orders = Order::latest()
        ->when(in_array($status, ['waiting', 'accepted', 'cooking', 'ready_to_deliver', 'delivering', 'completed'], true), function ($query) use ($status) {
            return $query->where('status', $status);
        })
        ->get();

    $couriers = Courier::withCount('orders')->orderBy('name')->get();
    $payouts = CourierPayout::with('courier')->latest()->get();
    $managedMenu = MenuItem::orderBy('category')->get();
    $reportFrom = request()->query('from', now()->startOfMonth()->toDateString());
    $reportTo = request()->query('to', now()->toDateString());
    try {
        $reportFromDate = Carbon::parse($reportFrom)->startOfDay();
        $reportToDate = Carbon::parse($reportTo)->endOfDay();
    } catch (\Exception $exception) {
        $reportFrom = now()->startOfMonth()->toDateString();
        $reportTo = now()->toDateString();
        $reportFromDate = now()->startOfMonth()->startOfDay();
        $reportToDate = now()->endOfDay();
    }
    $reportOrders = Order::with('items')
        ->whereIn('status', ['completed', 'confirmed'])
        ->whereBetween('created_at', [$reportFromDate, $reportToDate])
        ->latest()
        ->get();
    $reportRevenue = $reportOrders->sum('total');
    $reportDelivery = $reportOrders->count() * 1000;
    $reportPayouts = CourierPayout::whereBetween('created_at', [$reportFromDate, $reportToDate])->sum('amount');
    $reportProfit = $reportRevenue - $reportPayouts;
    $bestSellingMenus = OrderItem::whereHas('order', function ($query) use ($reportFromDate, $reportToDate) {
            $query->whereIn('status', ['completed', 'confirmed'])
                ->whereBetween('created_at', [$reportFromDate, $reportToDate]);
        })
        ->selectRaw('menu_name, SUM(quantity) as total_quantity, SUM(unit_price * quantity) as total_sales')
        ->groupBy('menu_name')
        ->orderByDesc('total_quantity')
        ->limit(10)
        ->get();

    return view('admin', compact('orders', 'status', 'couriers', 'payouts', 'managedMenu', 'reportFrom', 'reportTo', 'reportOrders', 'reportRevenue', 'reportDelivery', 'reportPayouts', 'reportProfit', 'bestSellingMenus'));
})->name('admin.dashboard');

Route::post('/feedback/whatsapp', function (Request $request) {
    $data = $request->validate([
        'name' => ['required', 'string', 'max:120'],
        'message' => ['required', 'string', 'max:1000'],
    ]);
    $adminPhone = \App\Services\WhatsAppService::normalizePhone(config('services.whatsapp.admin_phone', ''));
    if (!$adminPhone) {
        return back()->withErrors(['feedback' => 'Nomor WhatsApp admin belum dikonfigurasi.']);
    }
    $message = "Halo Admin 3GFood, saya {$data['name']}.\n\nKritik dan saran:\n{$data['message']}";
    return redirect()->away('https://wa.me/'.$adminPhone.'?text='.rawurlencode($message));
})->name('feedback.whatsapp');

Route::middleware('auth')->delete('/admin/orders', function () {
    $orderCount = Order::count();

    \Illuminate\Support\Facades\DB::transaction(function () {
        Order::query()->delete();
    });

    return back()->with('success', $orderCount.' pesanan berhasil dihapus. Daftar pesanan sekarang kosong.');
})->name('admin.orders.clear');

Route::middleware('auth')->post('/admin/menu', function (Request $request) {
    $data = $request->validate([
        'name' => 'required|string|max:120',
        'category' => 'required|in:Paket Nasi Liwet,Lauk Utama,Menu Tambahan',
        'price' => 'required|integer|min:0',
        'description' => 'nullable|string',
        'is_available' => 'nullable|boolean',
        'image' => 'nullable|image|max:2048',
    ]);
    $data['is_available'] = $request->boolean('is_available', true);
    $imagePath = $request->file('image')?->store('menu', 'public');
    $data['image_url'] = $imagePath ? asset('storage/' . $imagePath) : null;
    unset($data['image']);
    MenuItem::create($data);
    return back()->with('success', 'Menu berhasil ditambahkan.');
})->name('admin.menu.store');

Route::middleware('auth')->post('/admin/couriers', function (Request $request) {
    Courier::create($request->validate(['name' => 'required|string|max:120', 'phone' => 'required|string|max:30']));
    return back()->with('success', 'Kurir berhasil ditambahkan.');
})->name('admin.couriers.store');

Route::middleware('auth')->patch('/admin/orders/{order}/accept', function (Order $order) {
    if ($order->status === 'waiting' || $order->status === 'waiting_owner' || $order->status === 'waiting_payment') {
        $order->update(['status' => 'accepted', 'accepted_at' => now()]);
    }
    return back()->with('success', 'Pesanan diterima dan mulai disiapkan.');
})->name('admin.orders.accept');

Route::middleware('auth')->patch('/admin/orders/{order}/cook', function (Order $order) {
    if ($order->status === 'accepted') {
        $order->update(['status' => 'cooking', 'cooking_at' => now()]);
    }
    return back()->with('success', 'Pesanan sedang dimasak.');
})->name('admin.orders.cook');

Route::middleware('auth')->patch('/admin/orders/{order}/cooked', function (Order $order) {
    if ($order->status === 'cooking') {
        \Illuminate\Support\Facades\DB::transaction(function () use ($order) {
            $order->update(['status' => 'ready_to_deliver', 'cooked_at' => now()]);
            
            $message = "Pesanan #{$order->order_code} sudah siap diantar. Terima kasih sudah memesan.";
            $existingLog = $order->whatsappLogs()->where('status', 'sent')->first();
            
            if (!$existingLog) {
                \App\Services\WhatsAppService::sendMessage($order, $message);
            }
        });

        $order->load('whatsappLogs');
        $latestLog = $order->latestWhatsappLog;
        if ($latestLog && $latestLog->status === 'sent') {
            return back()->with('success', 'Pesanan berhasil diubah menjadi Siap Diantar dan notifikasi WhatsApp berhasil dikirim.');
        } else {
            return back()->with('success', 'Pesanan selesai dimasak dan siap diantar. (Gagal kirim notifikasi WhatsApp)');
        }
    }
    return back();
})->name('admin.orders.cooked');

Route::middleware('auth')->post('/admin/orders/{order}/resend-wa', function (Order $order) {
    $message = "Pesanan #{$order->order_code} sudah siap diantar. Terima kasih sudah memesan.";
    \App\Services\WhatsAppService::sendMessage($order, $message);
    
    $order->load('whatsappLogs');
    $latestLog = $order->latestWhatsappLog;
    if ($latestLog && $latestLog->status === 'sent') {
        return back()->with('success', 'Notifikasi WhatsApp berhasil dikirim ulang.');
    } else {
        return back()->with('success', 'Gagal kirim ulang notifikasi WhatsApp. Pastikan kredensial benar.');
    }
})->name('admin.orders.resend_wa');

Route::middleware('auth')->patch('/admin/orders/{order}/courier', function (Request $request, Order $order) {
    $request->validate(['courier_id' => 'required|exists:couriers,id']);
    if ($order->status === 'ready_to_deliver') {
        $order->update([
            'courier_id' => $request->courier_id,
            'status' => 'delivering',
            'delivering_at' => now()
        ]);
        return back()->with('success', 'Kurir berhasil ditetapkan dan pesanan mulai diantar.');
    }
    
    $order->update(['courier_id' => $request->courier_id]);
    return back()->with('success', 'Kurir pesanan berhasil diperbarui.');
})->name('admin.orders.courier');

Route::middleware('auth')->patch('/admin/orders/{order}/complete', function (Order $order) {
    if ($order->status === 'delivering' || $order->status === 'ready_to_deliver') {
        $order->update(['status' => 'completed', 'completed_at' => now()]);
    }
    return back()->with('success', 'Pesanan ditandai selesai.');
})->name('admin.orders.complete');

Route::middleware('auth')->post('/admin/payouts', function (Request $request) {
    CourierPayout::create($request->validate([
        'courier_id' => 'required|exists:couriers,id',
        'amount' => 'required|integer|min:1',
        'period' => 'required|string|max:80',
        'notes' => 'nullable|string|max:255',
    ]));

    return back()->with('success', 'Pencairan kurir berhasil dibuat.');
})->name('admin.payouts.store');

Route::middleware('auth')->delete('/admin/payouts', function () {
    $payoutCount = CourierPayout::count();
    CourierPayout::query()->delete();

    return back()->with('success', $payoutCount.' riwayat pencairan berhasil dihapus.');
})->name('admin.payouts.clear');

Route::middleware('auth')->patch('/admin/payouts/{payout}/paid', function (CourierPayout $payout) {
    $payout->update(['status' => 'paid']);

    return back()->with('success', 'Pencairan kurir ditandai sudah dibayar.');
})->name('admin.payouts.paid');

Route::middleware('auth')->get('/admin/orders/{order}/receipt', function (Order $order) {
    return view('receipt', compact('order'));
})->name('admin.orders.receipt');

Route::middleware('auth')->patch('/admin/couriers/{courier}/reset-orders', function (Courier $courier) {
    $completedStatuses = ['completed', 'confirmed'];
    $completedOrders = $courier->orders()->whereIn('status', $completedStatuses)->update(['courier_id' => null]);

    return back()->with('success', 'Total pengantaran kurir '.$courier->name.' berhasil direset menjadi 0 ('.$completedOrders.' pesanan selesai diarsipkan).');
})->name('admin.couriers.reset-orders');

Route::middleware('auth')->patch('/admin/menu/{menu}', function (Request $request, MenuItem $menu) {
    $data = $request->validate([
        'name' => 'required|string|max:120',
        'category' => 'required|in:Paket Nasi Liwet,Lauk Utama,Menu Tambahan',
        'price' => 'required|integer|min:0',
        'description' => 'nullable|string',
        'is_available' => 'nullable|boolean',
        'image' => 'nullable|image|max:2048',
    ]);
    $data['is_available'] = $request->boolean('is_available', $menu->is_available);
    if ($request->file('image')) {
        $imagePath = $request->file('image')->store('menu', 'public');
        $data['image_url'] = asset('storage/' . $imagePath);
    }
    unset($data['image']);
    $menu->update($data);
    return back()->with('success', 'Menu berhasil diupdate.');
})->name('admin.menu.update');

Route::middleware('auth')->delete('/admin/menu/{menu}', function (MenuItem $menu) {
    $menu->delete();
    return back()->with('success', 'Menu berhasil dihapus.');
})->name('admin.menu.delete');

Route::middleware('auth')->patch('/admin/couriers/{courier}', function (Request $request, Courier $courier) {
    $courier->update($request->validate(['name' => 'required|string|max:120', 'phone' => 'required|string|max:30']));
    return back()->with('success', 'Data kurir berhasil diupdate.');
})->name('admin.couriers.update');

Route::middleware('auth')->delete('/admin/couriers/{courier}', function (Courier $courier) {
    $courier->delete();
    return back()->with('success', 'Kurir berhasil dihapus.');
})->name('admin.couriers.delete');
