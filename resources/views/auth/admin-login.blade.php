<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center vh-100 bg-light">

    @auth
        <div class="card p-4 shadow-sm" style="min-width: 300px;">
            <h4 class="mb-3">You're already logged in</h4>
            <p class="mb-2">Using email, <strong>{{ Auth::user()->email }}</strong></p>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-danger w-100" type="submit">Logout</button>
            </form>
        </div>
    @else
        <div class="card p-4 shadow-sm" style="min-width: 300px;">
            <h4 class="mb-3">Admin Login</h4>
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif
            <form method="POST" action="/login">
                @csrf
                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email" required autofocus>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <button class="btn btn-primary w-100" type="submit">Login</button>
            </form>
        </div>
    @endauth

</body>
</html>
