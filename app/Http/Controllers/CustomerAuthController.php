<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
     */
    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();

        return redirect()->route('customer.login')->with('success', 'Anda telah berhasil keluar dari akun pembeli.');
    }
}
