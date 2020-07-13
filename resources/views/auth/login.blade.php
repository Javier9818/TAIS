<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <link href="{{ asset('assets/css/login.css') }}" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="login-content">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <img src="{{ asset('assets/img/avatar.svg') }}">
                <h2 class="title">Bienvenido</h2>
                   <div class="input-div one">
                      <div class="i">
                              <i class="fas fa-user"></i>
                      </div>
                      <div class="div">
                        <input id="email" type="email" class="input" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Correo electrónico">
                        @error('email')
                            {{-- <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span> --}}
                        @enderror
                      </div>
                   </div>
                   <div class="input-div pass">
                      <div class="i">
                           <i class="fas fa-lock"></i>
                      </div>
                      <div class="div">
                            <input id="password" type="password" class="input" name="password" required autocomplete="current-password" placeholder="Contraseña">
                            @error('password')
                                {{-- <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span> --}}
                            @enderror
                     </div>
                </div>
                {{-- <a href="#">Forgot Password?</a> --}}
                <input type="submit" class="btn" value="Iniciar Sesión">
            </form>
        </div>
    </div>
    <script src="https://kit.fontawesome.com/a81368914c.js"></script>
    <script src="{{ asset('assets/js/login.js') }}"></script>
</body>
</html>
