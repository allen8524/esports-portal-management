<head>
  <meta charset="UTF-8">
  <meta name="description" content="Specer Template">
  <meta name="keywords" content="Specer, unica, creative, html">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>@yield('title', 'Nexus')</title>

  {{-- Google Font --}}
  <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900&display=swap" rel="stylesheet">

  {{-- Vendor CSS --}}
  <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/font-awesome.min.css') }}" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/owl.carousel.min.css') }}" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/magnific-popup.css') }}" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/slicknav.min.css') }}" type="text/css">

  {{-- Base / Layout --}}
  <link rel="stylesheet" href="{{ asset('css/style.css') }}" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/partials.css') }}" type="text/css">

  {{-- Page specific --}}
  <link rel="stylesheet" href="{{ asset('css/teams.css') }}" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/matches.css') }}" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/account.css') }}" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/legal.css') }}" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/news.css') }}" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/rankings.css') }}" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/players.css') }}" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/auth.css') }}" type="text/css">

  {{-- Home / 섹션 전용 --}}
  <link rel="stylesheet" href="{{ asset('css/sections.css') }}" type="text/css">

  <link rel="icon" href="{{ asset('logo.ico') }}">
  @stack('styles')
</head>
