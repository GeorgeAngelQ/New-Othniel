@extends('layouts.app')

@section('header')
  <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
    {{ __('Tienda Othniel') }}
  </h2>
@endsection

@section('content')
<div class="py-10 px-6">
  <main class="max-w-7xl mx-auto">
    <h2 class="text-3xl font-bold mb-6 text-gray-900 dark:text-white text-center">
      Nuestros productos
    </h2>

    <!-- Grid de productos -->
    <section id="product-grid"
      class="grid gap-6 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-5">
      <!-- Productos cargados por JS -->
    </section>
    <div class="flex justify-center items-center gap-3 mt-6">
    <button id="prevPage"
        class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 text-sm disabled:opacity-50">
        ◀ Anterior
    </button>

    <span id="currentPage" class="text-gray-700 text-sm">Página 1</span>

    <button id="nextPage"
        class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 text-sm disabled:opacity-50">
        Siguiente ▶
    </button>
</div>
  </main>
</div>
@endsection
@section('scripts')
    @vite('resources/js/pages/home.js')
@endsection
