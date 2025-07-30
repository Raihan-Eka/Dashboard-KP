<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EbisCare</title>
    <style>
        /* General Body Styling */
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #4a4e54; /* Warna abu-abu gelap dari gambar */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #333;
        }

        /* Main Container */
        .main-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            padding: 20px;
        }

        /* Logo Styling */
        .logo {
            font-size: 2.5rem;
            font-weight: bold;
            color: #d9534f; /* Warna merah dari logo */
            margin-bottom: 30px;
            text-transform: uppercase;
        }

        /* Login Form Box */
        .login-box {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }

        /* Form Group for labels and inputs */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            color: #555;
            text-transform: uppercase;
        }

        .form-group input[type="text"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box; /* Important for padding */
            font-size: 1rem;
        }

        /* Terms of Use Group */
        .terms-group {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            font-size: 0.9rem;
        }

        .terms-group input[type="checkbox"] {
            margin-right: 10px;
            width: 16px;
            height: 16px;
        }

        .terms-group a {
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
        }

        .terms-group a:hover {
            text-decoration: underline;
        }

        /* Login Button */
        .login-button {
            width: 100%;
            padding: 12px;
            background-color: #c94c4c; /* Warna merah dari tombol */
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-button:hover {
            background-color: #b84444;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            color: #c0c0c0;
            font-size: 0.75rem;
            text-align: center;
        }
        
        /* Error messages styling */
        .alert-danger {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
    </style>
</head>
<body>

    <div class="main-container">
        <div class="logo">TELKOMCARE</div>

        <div class="login-box">
            
            @if ($errors->any())
                <div class="alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('status'))
                <div style="color: green; margin-bottom: 15px;">
                    {{ session('status') }}
                </div>
            @endif
            
            <form action="{{ route('login') }}" method="POST">
                @csrf <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}" required>
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="terms-group">
                    <input type="checkbox" id="agree" name="agree" required>
                    <label for="agree">Agree (<a href="#">Term of Use</a>)</label>
                </div>

                <button type="submit" class="login-button">Login</button>
            </form>
        </div>

        <div class="footer">
            ©2025 EbisCare™ - PT Telkom Indonesia Tbk.
        </div>
    </div>

</body>
</html>