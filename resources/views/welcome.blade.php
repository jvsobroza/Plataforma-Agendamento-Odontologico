<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sante Oral | Login</title>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>

    <div class="login-wrapper">
        <div class="brand-badge">
            <img src="{{ asset('images/logo.png') }}" alt="Sante Oral Odontologia">
        </div>
        <div class="login-card">
            <h1 class="login-title">Login</h1>
            @if (session('status'))
            <div class="alert-status">
                {{ session('status') }}
            </div>
            @endif

            <form action="{{ route('login') }}" method="POST" novalidate>
                @csrf

                <div class="form-row">
                    <label for="email" class="form-label">Email:</label>
                    <input
                        type="email"
                        class="form-control @error('email') is-invalid @enderror"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Insira o Email"
                        required
                        autofocus>
                    @error('email')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row">
                    <label for="senha" class="form-label">Senha:</label>
                    <input
                        type="password"
                        class="form-control @error('senha') is-invalid @enderror"
                        id="senha"
                        name="senha"
                        placeholder="Insira a Senha"
                        required>
                    @error('senha')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-login">Logar</button>
            </form>

        </div>

    </div>

</body>

</html>