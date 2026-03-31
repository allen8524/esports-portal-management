@extends('layouts.main')

@section('title', 'DB 테이블 목록')
@section('body_class', 'admin-page')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/admin-db.css') }}">
@endpush

@section('content')
<div class="admin-db-shell">
  <div class="admin-db-header">
    <h3 class="admin-db-title">DB 테이블 목록</h3>
    <p class="admin-db-subtitle">
      현재 데이터베이스에 존재하는 모든 테이블을 한눈에 확인할 수 있습니다.
    </p>
  </div>

  <ul class="list-group admin-db-list">
    @foreach($tables as $table)
      <li class="list-group-item admin-db-item d-flex justify-content-between align-items-center">
        <div class="admin-db-name">
          <span class="admin-db-dot"></span>
          <span class="admin-db-text">{{ $table }}</span>
        </div>
        <a href="{{ route('admin.db.show', $table) }}" class="btn btn-sm btn-wine-outline">
          <i class="fa fa-table me-1"></i> 데이터 보기
        </a>
      </li>
    @endforeach
  </ul>
</div>
@endsection
