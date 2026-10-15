<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Struk {{ $order->order_code }} | 3G FOOD</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #ede5d8; color: #1c1917; margin: 0; padding: 20px 12px; }
        .receipt { width: min(100%, 440px); margin: 20px auto; padding: 30px 24px; background: #fdfbf7; border-radius: 20px; border: 1px solid #d5c7b2; box-shadow: 0 10px 30px rgba(40,20,10,0.06); }
        .logo-wrap { text-align: center; margin-bottom: 12px; }
        .logo-img { width: 68px; height: 68px; border-radius: 16px; object-fit: contain; background: #fff; border: 1px solid #d5c7b2; padding: 4px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .brand { text-align: center; color: #780d20; font-family: 'Outfit', sans-serif; font-size: 26px; font-weight: 800; letter-spacing: -0.5px; }
        .brand span { color: #e50027; }
        .muted { color: #786d5e; text-align: center; font-size: 12px; margin-top: 2px; }
        .line { border-top: 1px dashed #d5c7b2; margin: 16px 0; }
        .row { display: flex; justify-content: space-between; align-items: flex-start; margin: 8px 0; font-size: 13px; color: #44403c; }
        .row b { color: #1c1917; }
        .items-table { width: 100%; border-collapse: collapse; margin: 14px 0 6px; font-size: 13px; }
        .items-table th { text-align: left; font-size: 11px; text-transform: uppercase; color: #786d5e; padding-bottom: 8px; border-bottom: 1px solid #e5dbcb; }
        .items-table td { padding: 8px 0; border-bottom: 1px dashed #ede2d2; }
        .items-table tr:last-child td { border-bottom: 0; }
        .total-row { font-size: 16px; font-weight: 800; color: #e50027; margin-top: 10px; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 6px; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .badge-cash { background: #ffedd5; color: #c2410c; }
        .badge-bank { background: #e0f2fe; color: #0369a1; }
        .badge-digital { background: #f3e8ff; color: #7e22ce; }
        .print-btn { display: block; width: 100%; padding: 14px; border: 0; background: #e50027; color: white; border-radius: 12px; font-weight: 700; font-size: 14px; margin-top: 24px; cursor: pointer; transition: background .2s; }
        .print-btn:hover { background: #b3001a; }
        @media print {
            body { background: #fff; padding: 0; }
            .receipt { width: 100%; max-width: 100%; margin: 0; box-shadow: none; border: none; padding: 10px; }
            .print-btn { display: none; }
        }
    </style>
</head>
<body>
    <main class="receipt">
        <div class="logo-wrap">
            <img src="{{ asset('images/logo.jpg') }}" alt="3GFood" class="logo-img">
        </div>
        <div class="brand">3G<span>Food</span></div>
        <p class="muted">Saveur & Tradition • Struk Resmi Pembelian</p>

        <div class="line"></div>

        <div class="row"><span>Nomor Pesanan</span><b>#{{ $order->order_code }}</b></div>
        <div class="row"><span>Tanggal & Waktu</span><span>{{ $order->created_at->format('d/m/Y H:i') }} WIB</span></div>
        <div class="row"><span>Nama Pelanggan</span><b>{{ $order->customer_name }}</b></div>
        <div class="row"><span>Nomor HP</span><span>{{ $order->phone }}</span></div>
        <div class="row"><span>Alamat Antar</span><span style="text-align:right;max-width:230px">{{ $order->address }}</span></div>
        @if($order->courier)
        <div class="row"><span>Kurir Pengantar</span><b>{{ $order->courier->name }} ({{ $order->courier->phone }})</b></div>
        @endif
        <div class="row">
            <span>Metode Bayar</span>
            <span class="badge {{ $order->payment_method === 'cash' ? 'badge-cash' : ($order->payment_method === 'bank' ? 'badge-bank' : 'badge-digital') }}">
                {{ ['cash' => 'COD / Tunai', 'bank' => 'Transfer Bank', 'digital' => 'QRIS Digital'][$order->payment_method] ?? $order->payment_method }}
            </span>
        </div>

        <div class="line"></div>

        <!-- ITEMS BREAKDOWN -->
        @if($order->items && $order->items->count() > 0)
        <table class="items-table">
            <thead>
                <tr>
                    <th>Menu Dipesan</th>
                    <th style="text-align:center">Qty</th>
                    <th style="text-align:right">Harga</th>
                    <th style="text-align:right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>
                        <b>{{ $item->name }}</b>
                        @if($item->notes)<div style="font-size:11px;color:#786d5e">Catatan: {{ $item->notes }}</div>@endif
                    </td>
                    <td style="text-align:center">{{ $item->quantity }}</td>
                    <td style="text-align:right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td style="text-align:right;font-weight:600">Rp {{ number_format($item->total ?? ($item->price * $item->quantity), 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <div class="line"></div>

        <div class="row total-row">
            <span>TOTAL PEMBAYARAN</span>
            <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
        </div>

        <div class="line"></div>

        <p class="muted" style="margin-top:14px;line-height:1.5">
            Terima kasih telah memesan di <strong>3GFood</strong>.<br>
            Cita rasa autentik disajikan hangat khusus untuk Anda.
        </p>

        <button class="print-btn" onclick="window.print()">Cetak / Simpan PDF</button>
    </main>
</body>
</html>
