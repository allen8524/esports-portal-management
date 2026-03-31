@extends('layouts.main')

@section('title', '로그인')
@section('body_class', 'auth-page')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@section('content')
<div class="auth-shell">

  <div class="auth-header text-center">
    <h3 class="auth-title">로그인</h3>
    <p class="auth-subtitle">
      아래에 <strong>admin@induk.ac.kr / asdf1234</strong>를 입력해보세요!
    </p>
  </div>

  @if(session('status'))
    <div class="alert alert-success py-2 mb-3 auth-alert">
      {{ session('status') }}
    </div>
  @endif

  <div class="auth-card">
    <form method="post" action="{{ route('login') }}">
      @csrf

      <div class="mb-3">
        <label class="form-label auth-label">이메일</label>
        <input type="email"
               name="email"
               value="{{ old('email') }}"
               class="form-control auth-input @error('email') is-invalid @enderror"
               required autofocus>
        @error('email')
          <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <label class="form-label auth-label">비밀번호</label>
        <input type="password"
               name="password"
               class="form-control auth-input @error('password') is-invalid @enderror"
               required>
        @error('password')
          <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3 form-check">
        <input type="checkbox" name="remember" class="form-check-input" id="remember">
        <label class="form-check-label auth-remember-label" for="remember">
          로그인 상태 유지
        </label>
      </div>

      <button type="submit" class="btn btn-auth w-100">
        <i class="fa fa-sign-in me-1"></i> 로그인
      </button>

      <div class="mt-3 text-center">
        <a href="{{ route('register') }}" class="auth-link-muted">
          계정이 없나요? <span class="fw-semibold">회원가입</span>
        </a>
      </div>
    </form>
  </div>

</div>
@endsection
