<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Othinel') }}</title>
    <!-- Fuente -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"/>

    <!-- Estilos / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('scripts')
  </head>

  <body class="font-sans antialiased bg-[#f9f5f2] min-h-screen flex flex-col text-[#4a3b2f]">

    <!-- Navegación -->
    @include('layouts.navigation')

    <!-- Encabezado opcional -->


    <!-- Contenido principal -->
    <main class="flex-grow max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-6">
      @yield('content')
    </main>

    <!-- Footer -->
    <footer class="text-center py-5 text-[#6b5846] border-t border-[#d9c8b6] bg-[#f0e8e0]">
      <small>© <span id="year"></span> <strong>Othniel</strong>. Todos los derechos reservados.</small>
    </footer>

    <!-- JS para el año -->
    <script>
      document.getElementById("year").textContent = new Date().getFullYear();
    </script>
  </body>
</html>
