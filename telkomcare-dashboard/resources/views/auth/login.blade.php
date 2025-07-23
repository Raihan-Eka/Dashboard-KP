<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Telkomcare</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

        body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background-color: #4A5568; /* Warna abu-abu kebiruan gelap */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #2D3748;
        }

        .login-container {
            background-color: white;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
            box-sizing: border-box;
        }

        .logo {
            width: 150px;
            margin-bottom: 30px;
        }

        .login-form .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .login-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #4A5568;
            font-size: 0.875rem;
        }

        .login-form input[type="text"],
        .login-form input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #CBD5E0;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 1rem;
        }
        .login-form input:focus {
            outline: none;
            border-color: #E53E3E;
            box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.2);
        }

        .agree-group {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin-bottom: 25px;
            font-size: 0.875rem;
        }
        .agree-group input[type="checkbox"] {
            margin-right: 10px;
            width: 16px;
            height: 16px;
            accent-color: #E53E3E;
        }
        .agree-group label {
            margin-bottom: 0;
        }
        .agree-group a {
            color: #E53E3E;
            text-decoration: none;
            font-weight: 500;
            margin-left: 4px;
        }
        .agree-group a:hover {
            text-decoration: underline;
        }

        .login-button {
            width: 100%;
            padding: 14px;
            background-color: #D34C4C; /* Warna merah dari gambar */
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .login-button:hover {
            background-color: #C0392B;
        }

        .footer {
            margin-top: 30px;
            font-size: 0.8rem;
            color: #A0AEC0;
        }

        /* Blok untuk menampilkan pesan error */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            color: #721c24;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            text-align: left;
            font-size: 0.9rem;
        }
        .alert ul {
            margin: 0;
            padding-left: 20px;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <!-- Anda bisa ganti src dengan path logo Anda atau biarkan seperti ini -->
        <img src="{{ asset('images/EbisCare_logo.svg') }}" alt="Logo Perusahaan Anda" class="logo" style="width: 200px; height: auto; margin: 0 auto 20px;">

        <form method="POST" action="{{ route('login') }}" class="login-form">
            @csrf <!-- Token Keamanan Laravel -->

            <!-- Menampilkan semua error validasi di sini -->
            @if($errors->any())
                <div class="alert">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form-group">
                <label for="username">USERNAME :</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">PASSWORD:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="agree-group">
                <input type="checkbox" id="agree" name="agree" value="1">
                <label for="agree">Agree (</label><a href="#">Term of Use</a>)
            </div>

            <button type="submit" class="login-button">Login</button>
        </form>

        <div class="footer">
            ©2025 Telkomcare™ - PT Telkom Indonesia Tbk.
        </div>
    </div>

</body>
</html>
