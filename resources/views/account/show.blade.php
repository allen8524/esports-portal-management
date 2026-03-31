@extends('layouts.main')

@section('title', '내 계정')
@section('body_class', 'account-page')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/account.css') }}">
@endpush

@section('content')
<div class="account-shell">

  {{-- 헤더 --}}
  <div class="account-header">
    <h3 class="account-title">내 계정</h3>
    <p class="account-subtitle">
      회원 정보와 가입일을 확인할 수 있습니다.
    </p>
  </div>

  @if(session('status'))
    <div class="alert alert-success py-2 mb-3 account-alert">
      {{ session('status') }}
    </div>
  @endif

  {{-- 정보 카드 --}}
  <div class="account-card">
    <dl class="row mb-0 account-info">
      <dt class="col-sm-3">이름</dt>
      <dd class="col-sm-9">{{ $account->name }}</dd>

      <dt class="col-sm-3">이메일</dt>
      <dd class="col-sm-9">{{ $account->email }}</dd>

      <dt class="col-sm-3">가입일</dt>
      <dd class="col-sm-9">
        @if($account->created_at)
          {{ $account->created_at->format('Y-m-d H:i') }}
        @endif
      </dd>
    </dl>
  </div>

  <div class="mt-3 text-end">
    <a href="{{ route('account.edit') }}" class="btn btn-account">
      <i class="fa fa-pencil me-1"></i> 정보 수정
    </a>
  </div>

</div>
@endsection
