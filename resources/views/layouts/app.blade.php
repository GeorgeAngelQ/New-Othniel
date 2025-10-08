<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Othinel Shop') }}</title>
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
</head>
<body class="bg-gray-50 text-gray-800">

    <!-- Navbar -->
    <nav class="bg-white shadow p-4 flex justify-between items-center">
        <a href="{{ url('/') }}" class="text-lg font-bold">🛍️ Othinel</a>
        <div class="space-x-4">
            @auth
                <a href="{{ route('products.index') }}">Productos</a>
                <a href="{{ route('cart.index') }}">Carrito</a>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-red-500">Cerrar sesión</button>
                </form>
            @else
                <a href="{{ route('login.form') }}">Iniciar sesión</a>
                <a href="{{ route('register.form') }}">Registrarse</a>
            @endauth
        </div>
    </nav>

    <!-- Contenido principal -->
    <main class="container mx-auto p-6">
        @yield('content')
    </main>

</body>
</html>
