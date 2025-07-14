<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Membuat instance controller baru.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Menampilkan formulir login aplikasi.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Menangani permintaan login yang masuk.
     */
    public function login(Request $request)
    {
        // 1. Validasi data yang dikirim dari form (diubah ke username)
        $this->validateLogin($request);

        // 2. Mencoba untuk mengautentikasi pengguna (menggunakan username)
        if (Auth::attempt($this->credentials($request))) {
            $request->session()->regenerate();
            return redirect()->intended('/home');
        }

        // 3. Jika autentikasi gagal, kembali ke form login dengan pesan error
        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Melakukan logout pengguna.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    /**
     * Memvalidasi input dari pengguna.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    protected function validateLogin(Request $request)
    {
        // Mengubah validasi dari 'email' menjadi 'username'
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
    }

    /**
     * Menyiapkan kredensial untuk percobaan autentikasi.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        // Mengubah pengambilan data dari 'email' menjadi 'username'
        return $request->only('username', 'password');
    }

    /**
     * Memberikan respons jika percobaan login gagal.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        // Mengubah field error dari 'email' menjadi 'username'
        throw ValidationException::withMessages([
            'username' => [trans('auth.failed')],
        ]);
    }
}
