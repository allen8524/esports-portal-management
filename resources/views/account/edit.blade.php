@extends('layouts.main')

@section('title', '계정 정보 수정')
@section('body_class', 'account-page')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/account.css') }}">
@endpush

@section('content')
<div class="account-shell">

  {{-- 헤더 --}}
  <div class="account-header">
    <h3 class="account-title">계정 정보 수정</h3>
    <p class="account-subtitle">
      이름, 이메일, 비밀번호를 변경할 수 있습니다.
    </p>
  </div>

  {{-- 전체 에러 한 줄로 보여주기 (옵션) --}}
  @if($errors->any())
    <div class="alert alert-danger py-2 mb-3 account-alert">
      입력값을 다시 확인해 주세요.
    </div>
  @endif

  {{-- 폼 카드 --}}
  <div class="account-card">
    <form method="post" action="{{ route('account.update') }}">
      @csrf
      @method('PUT')

      <div class="mb-3">
        <label class="form-label account-label">이름</label>
        <input type="text"
               name="name"
               value="{{ old('name', $account->name) }}"
               class="form-control account-input @error('name') is-invalid @enderror"
               required>
        @error('name')
          <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <label class="form-label account-label">이메일</label>
        <input type="email"
               name="email"
               value="{{ old('email', $account->email) }}"
               class="form-control account-input @error('email') is-invalid @enderror"
               required>
        @error('email')
          <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
      </div>

      <hr class="my-4 account-divider">

      <div class="mb-3">
        <label class="form-label account-label">새 비밀번호 (변경 시)</label>
        <input type="password"
               name="password"
               class="form-control account-input @error('password') is-invalid @enderror">
        @error('password')
          <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <label class="form-label account-label">새 비밀번호 확인</label>
        <input type="password"
               name="password_confirmation"
               class="form-control account-input">
      </div>

      <button type="submit" class="btn btn-account w-100">
        <i class="fa fa-save me-1"></i> 저장
      </button>
    </form>
  </div>

</div>
@endsection
