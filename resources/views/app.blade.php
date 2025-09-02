<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head class="mb-5">
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="csrf-token" content="{{ csrf_token() }}">

      <!-- Fonts -->
      <link rel="preconnect" href="https://fonts.bunny.net">
      <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

      <!-- Scripts -->
      @vite(['resources/css/app.css', 'resources/js/app.js'])

      {{-- Inertia will inject titles, meta, etc. --}}
      @inertiaHead
  </head>
  <body class="font-sans antialiased mt-5">
  <div class="mt-5">
      @inertia
      </div>
      @routes
  </body>
</html>
