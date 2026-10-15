<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class CustomerAuthController extends Controller
{
    /**
     * Tampilkan form login & sign up khusus pembeli / pelanggan.
     */
    public function showLoginForm()
    {
        if (Auth::guard('customer')->check()) {
            return redirect('/');
        }

        $activeTab = request()->query('tab', 'login');
        return view('customer.login', compact('activeTab'));
    }

    /**
     * Proses autentikasi login pembeli.
     */
    public function login(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('active_tab', 'login');
        }

        $credentials = $validator->validated();

        if (Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $customerName = Auth::guard('customer')->user()->name;
            return redirect('/')->with('success', "Selamat datang kembali, {$customerName}!");
        }

        return back()->withErrors([
            'login_error' => 'Email atau kata sandi tidak sesuai dengan akun terdaftar.',
        ])->withInput()->with('active_tab', 'login');
    }

    /**
     * Tampilkan form registrasi akun pembeli baru (tab register).
     */
    public function showRegisterForm()
    {
        if (Auth::guard('customer')->check()) {
            return redirect('/');
        }

        $activeTab = 'register';
        return view('customer.login', compact('activeTab'));
    }

    /**
     * Proses pendaftaran akun pembeli baru.
     */
    public function register(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:customers,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:25'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar sebagai pembeli.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('active_tab', 'register');
        }

        $data = $validator->validated();

        // Simpan data akun pelanggan baru dengan enkripsi bcrypt
        $customer = Customer::create([
            'name' => $data['name'],
            'email' => strtolower(trim($data['email'])),
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
        ]);

        // Otomatis login menggunakan guard customer
        Auth::guard('customer')->login($customer);
        $request->session()->regenerate();

        return redirect('/')->with('success', "Pendaftaran berhasil! Selamat datang di 3GFood, {$customer->name}.");
    }

    /**
     * Logout khusus pembeli (tidak mengganggu session admin).
     * Berjalan instan tanpa loading lama dan mengarahkan kembali ke beranda sebagai tamu.
     */
    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $request->session()->flash('success', 'Anda telah berhasil keluar dari akun pembeli.');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Anda telah berhasil keluar dari akun pembeli.',
                'redirect' => url('/?screen=home'),
            ]);
        }

        return redirect('/?screen=home')->with('success', 'Anda telah berhasil keluar dari akun pembeli.');
    }

    /**
     * Perbarui data profil, foto (galeri / jepret langsung), dan keamanan akun pembeli.
     */
    public function updateSettings(Request $request)
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();
        if (!$customer) {
            return redirect()->route('customer.login');
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:customers,email,' . $customer->id],
            'phone' => ['nullable', 'string', 'max:25'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120'],
            'avatar_base64' => ['nullable', 'string'],
            'remove_avatar' => ['nullable', 'boolean'],
            'current_password' => ['nullable', 'string'],
            'new_password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'avatar.image' => 'File foto profil harus berupa gambar.',
            'avatar.max' => 'Ukuran foto profil maksimal 5MB.',
            'new_password.min' => 'Password baru minimal 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Hapus avatar jika dipilih opsi hapus
        if ($request->boolean('remove_avatar')) {
            if ($customer->avatar && !str_starts_with($customer->avatar, 'http') && Storage::disk('public')->exists($customer->avatar)) {
                Storage::disk('public')->delete($customer->avatar);
            }
            $customer->avatar = null;
        }

        // Simpan foto dari upload file galeri / kamera native
        if ($request->hasFile('avatar')) {
            if ($customer->avatar && !str_starts_with($customer->avatar, 'http') && Storage::disk('public')->exists($customer->avatar)) {
                Storage::disk('public')->delete($customer->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $customer->avatar = $avatarPath;
        }

        // Simpan foto yang diambil langsung dari web camera snapshot (Base64)
        if ($request->filled('avatar_base64') && !$request->hasFile('avatar')) {
            $base64Data = $request->input('avatar_base64');
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $typeMatches)) {
                $rawBase64 = substr($base64Data, strpos($base64Data, ',') + 1);
                $ext = strtolower($typeMatches[1]);
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $decodedImage = base64_decode($rawBase64);
                    if ($decodedImage !== false) {
                        if ($customer->avatar && !str_starts_with($customer->avatar, 'http') && Storage::disk('public')->exists($customer->avatar)) {
                            Storage::disk('public')->delete($customer->avatar);
                        }
                        $filename = 'avatars/cam_' . uniqid() . '.' . ($ext === 'jpeg' ? 'jpg' : $ext);
                        Storage::disk('public')->put($filename, $decodedImage);
                        $customer->avatar = $filename;
                    }
                }
            }
        }

        // Perbarui identitas
        $customer->name = $request->input('name');
        $customer->email = strtolower(trim($request->input('email')));
        $customer->phone = $request->input('phone');

        // Perbarui kata sandi jika diisi
        if ($request->filled('new_password')) {
            if ($request->filled('current_password')) {
                if (!Hash::check($request->input('current_password'), $customer->password)) {
                    return back()->withErrors(['current_password' => 'Kata sandi saat ini tidak cocok.'])->withInput();
                }
            }
            $customer->password = Hash::make($request->input('new_password'));
        }

        $customer->save();

        return redirect('/?screen=settings')->with('success', 'Pengaturan akun Anda berhasil disimpan!');
    }

    /**
     * Hapus foto profil pengguna.
     */
    public function removeAvatar()
    {
        /** @var Customer $customer */
        $customer = Auth::guard('customer')->user();
        if ($customer) {
            if ($customer->avatar && !str_starts_with($customer->avatar, 'http') && Storage::disk('public')->exists($customer->avatar)) {
                Storage::disk('public')->delete($customer->avatar);
            }
            $customer->avatar = null;
            $customer->save();
        }

        return redirect('/?screen=settings')->with('success', 'Foto profil berhasil dihapus.');
    }
}
