<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <script>
            window.location.href = "/login";
        </script>
    <div class="button-container">
        <a href="{{ route('login') }}" class="button primary">Login</a>
        <a href="{{ route('register') }}" class="button secondary">Register</a>
    </div>
</body>
</html>
