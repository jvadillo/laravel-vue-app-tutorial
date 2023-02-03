<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <!-- Scripts -->
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    
</head>
<body>
<!-- Base from: https://startbootstrap.com/previews/blog-home -->

<!-- Not 100% Responsive navbar for simplification-->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <!-- Logo zone -->
    <a class="navbar-brand" href="#!">Laravel + Vue.js Blog</a>

    <!-- Menu links -->
    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
      <li class="nav-item"><a class="nav-link" href="#!">Inicio</a></li>
      <li class="nav-item"><a class="nav-link" href="#!">Contact</a></li>
      <li class="nav-item"><a class="nav-link" href="#">View en Github</a></li>
    </ul>

  </div>
</nav>

<!-- Page content-->
<div id="app">
</div>

<!-- Footer-->
<footer class="py-5 bg-dark">
  <div class="container">
    <p class="m-0 text-center text-white">Copyright &copy; Your Website 2022</p>
  </div>
</footer>
</body>
</html>