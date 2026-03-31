@extends('layouts.main')

@section('title', '테이블: ' . $table)
@section('body_class', 'admin-page')


@push('styles')
  <link rel="stylesheet" href="{{ asset('css/admin-db.css') }}">
@endpush

@section('content')
  <div class="admin-db-shell">

    {{-- 헤더 --}}
    <div class="admin-db-header d-flex justify-content-between align-items-center mb-3">
      <div>
        <h3 class="admin-db-title mb-0">테이블: {{ $table }}</h3>
        <p class="admin-db-subtitle">
          이 테이블의 레코드를 단순 조회합니다. (수정/삭제는 별도 관리 화면에서 처리해 주세요)
        </p>
      </div>
      <a href="{{ route('admin.db.index') }}" class="btn btn-sm btn-wine-outline">
        ← 테이블 목록
      </a>
    </div>

    {{-- 테이블 카드 --}}
    <div class="admin-db-table-card">
      <div class="table-responsive">
        <table class="table table-sm admin-db-table align-middle">
          <thead>
            <tr>
              @foreach($columns as $col)
                <th>{{ $col }}</th>
              @endforeach
            </tr>
          </thead>
          <tbody>
            @forelse($rows as $row)
              <tr>
                @foreach($columns as $col)
                  <td>{{ data_get($row, $col) }}</td>
                @endforeach
              </tr>
            @empty
              <tr>
                <td colspan="{{ count($columns) }}" class="text-center text-muted py-4">
                  데이터가 없습니다.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- 페이지네이션: 숫자만 직접 그리기 --}}
      @if ($rows->hasPages())
        <nav class="mt-3 d-flex justify-content-center admin-db-pagination">
          <ul class="pagination">
            @for ($page = 1; $page <= $rows->lastPage(); $page++)
              <li class="page-item {{ $page == $rows->currentPage() ? 'active' : '' }}">
                <a class="page-link" href="{{ $rows->url($page) }}">{{ $page }}</a>
              </li>
            @endfor
          </ul>
        </nav>
      @endif


    </div>

  </div>
@endsection