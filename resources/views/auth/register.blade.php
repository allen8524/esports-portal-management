@extends('layouts.main')

@section('title', '회원가입')
@section('body_class', 'auth-page')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endpush

@section('content')
<div class="auth-shell">

  <div class="auth-header text-center">
    <h3 class="auth-title">회원가입</h3>
    <p class="auth-subtitle">
      간단한 정보만 입력하면 바로 가입할 수 있습니다.
    </p>
  </div>

  <div class="auth-card">
    <form method="post" action="{{ route('register') }}">
      @csrf

      <div class="mb-3">
        <label class="form-label auth-label">이름</label>
        <input type="text"
               name="name"
               value="{{ old('name') }}"
               class="form-control auth-input @error('name') is-invalid @enderror"
               required autofocus>
        @error('name')
          <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <label class="form-label auth-label">이메일</label>
        <input type="email"
               name="email"
               value="{{ old('email') }}"
               class="form-control auth-input @error('email') is-invalid @enderror"
               required>
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

      <div class="mb-3">
        <label class="form-label auth-label">비밀번호 확인</label>
        <input type="password"
               name="password_confirmation"
               class="form-control auth-input"
               required>
      </div>

      <button type="submit" class="btn btn-auth w-100">
        <i class="fa fa-user-plus me-1"></i> 회원가입
      </button>

      <div class="mt-3 text-center">
        <a href="{{ route('login') }}" class="auth-link-muted">
          이미 계정이 있으신가요? <span class="fw-semibold">로그인</span>
        </a>
      </div>
    </form>
  </div>

</div>
@endsection
