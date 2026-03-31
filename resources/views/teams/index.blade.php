@extends('layouts.main')
@section('title', '팀 목록')
@section('body_class', 'teams-page')
@push('styles')
  <style>
    /* 팀 목록 페이지에서 페이지네이션 요약 문구 숨기기 */
    .teams-page p.small.text-muted {
      display: none !important;
    }
  </style>
@endpush
@section('content')
  <div class="container py-4">

    <div class="page-head d-flex justify-content-between align-items-center mb-3">
      <div>
        <h3 class="mb-1 page-title">&nbsp;팀 목록</h3>
        @if($teams->total() > 0)
          <div class="meta-wrap">
            <span class="meta">총 {{ number_format($teams->total()) }}개</span>
            <span class="meta"><i class="fa fa-layer-group me-1"></i>{{ $teams->perPage() }}개/페이지</span>
            <span class="meta">
              <i class="fa fa-copy me-1"></i>
              페이지 {{ number_format($teams->currentPage()) }} / {{ number_format($teams->lastPage()) }}
            </span>
          </div>
        @endif
      </div>
      <a class="btn btn-primary btn-sm" href="{{ route('teams.create') }}">
        <i class="fa fa-people-group me-1"></i>&nbsp;팀 등록
      </a>
    </div>

    <form method="get" class="teams-toolbar p-3 mb-3">
      <div class="row g-2 align-items-center">
        <div class="col-12 col-md-6">
          <div class="input-group input-group-sm">
            <span class="input-group-text bg-white"><i class="fa fa-search"></i></span>
            <input type="text" name="q" value="{{ $q }}" class="form-control form-control-sm" placeholder="팀명/슬러그/지역 검색">
          </div>
        </div>
        <div class="col-6 col-md-2 d-grid">
          <button class="btn btn-dark btn-sm" type="submit">검색</button>
        </div>
        <div class="col-6 col-md-2 d-grid">
          @if($q)
            <a href="{{ route('teams.index') }}" class="btn btn-outline-secondary btn-sm">초기화</a>
          @endif
        </div>
      </div>

      <div class="mt-2 d-flex flex-wrap gap-2 filter-pills">
        @if($q) <span class="pill active"><i class="fa fa-filter me-1"></i>검색: “{{ $q }}”</span> @endif
      </div>
    </form>

    @if($teams->count() === 0)
      <div class="empty text-center text-muted">
        <i class="fa fa-people-group fa-2x mb-2"></i>
        <div class="mb-1 fw-semibold">조건에 맞는 팀이 없습니다.</div>
        <div class="small">검색어와 필터를 바꿔 보세요.</div>
        <div class="mt-3">
          <a href="{{ route('teams.create') }}" class="btn btn-outline-primary btn-sm">
            <i class="fa fa-people-group me-1"></i>새 팀 등록
          </a>
        </div>
      </div>
    @else
      <div class="row teams-grid">
        @foreach($teams as $team)
          @php
            $logo = $team->logo_src;
            $abbr = trim($team->name) !== '' ? mb_substr($team->name, 0, 2) : 'TM';
          @endphp
          <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
            <div class="card players-card h-100 team-card">
              <div class="card-body">
                <div class="d-flex align-items-center gap-3">
                  <div class="logo-box">
                    @if($logo)
                      <img src="{{ $logo }}" alt="{{ $team->name }} logo" loading="lazy">
                    @else
                      <div class="logo-fallback">{{ $abbr }}</div>
                    @endif
                  </div>
                  <div class="flex-grow-1">
                    <div class="d-flex align-items-start justify-content-between">
                      <div>
                        <h6 class="card-title mb-1">{{ $team->name }}</h6>
                      </div>
                      @if($team->region)
                        <span class="badge state-pill bg-secondary">{{ $team->region }}</span>
                      @endif
                    </div>
                  </div>
                </div>
                <a href="{{ route('teams.show', $team) }}" class="stretched-link" aria-label="{{ $team->name }} 상세보기"
                  title="{{ $team->name }} 상세보기"></a>
              </div>
              <div class="card-footer actions d-flex align-items-center justify-content-between">
                <span class="small text-muted">등록일: {{ optional($team->created_at)->format('Y-m-d') }}</span>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <div class="mt-3 d-flex justify-content-center">
        {{ $teams->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
      </div>

    @endif

  </div>
@endsection
