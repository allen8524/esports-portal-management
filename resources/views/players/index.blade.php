@extends('layouts.main')
@section('title', '선수 목록')
@section('body_class', 'players-page')

@section('content')
  <div class="container py-4">

    {{-- 상단 헤더 --}}
    <div class="page-head d-flex justify-content-between align-items-center mb-3">
      <div>
        <h3 class="mb-1 page-title">&nbsp;선수 목록</h3>
        <div class="meta-wrap">
          @if($players->total() > 0)
            <span class="meta">총 {{ number_format($players->total()) }}명</span>
            <span class="meta"><i class="fa fa-layer-group me-1"></i>{{ $players->perPage() }}명/페이지</span>
            <span class="meta"><i class="fa fa-copy me-1"></i>페이지 {{ number_format($players->currentPage()) }} /
              {{ number_format($players->lastPage()) }}</span>
          @endif
        </div>
      </div>
      <a class="btn btn-primary btn-sm" href="{{ route('players.create') }}">
        <i class="fa fa-user-plus me-1"></i>&nbsp;선수 등록
      </a>
    </div>

    {{-- 필터 툴바 --}}
    <form method="get" class="players-toolbar p-3 mb-3">
      <div class="row g-2 align-items-center">
        <div class="col-12 col-md-4">
          <div class="input-group input-group-sm">
            <span class="input-group-text bg-white"><i class="fa fa-search"></i></span>
            <input type="text" name="q" value="{{ $q }}" class="form-control form-control-sm" placeholder="이름/닉네임 검색">
          </div>
        </div>

        <div class="col-6 col-md-2">
          <select name="role" class="form-select form-select-sm">
            <option value="">포지션 전체</option>
            @foreach($roles as $r)
              <option value="{{ $r }}" @selected($role === $r)>{{ $r }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-6 col-md-3">
          <select name="team_id" class="form-select form-select-sm">
            <option value="">팀 전체</option>
            @foreach($teams as $t)
              <option value="{{ $t->id }}" @selected((string) $teamId === (string) $t->id)>{{ $t->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-6 col-md-2 d-flex align-items-center gap-2">
          <div class="form-check ms-1">
            <input class="form-check-input" type="checkbox" id="only_active" name="only_active" value="1" {{ $active ? 'checked' : '' }}>
            <label class="form-check-label small" for="only_active">활동 중만</label>
          </div>
        </div>

        <div class="col-6 col-md-1 d-grid">
          <button class="btn btn-dark btn-sm" type="submit">검색</button>
        </div>
      </div>

      <div class="mt-2 d-flex flex-wrap gap-2 filter-pills">
        @if($q) <span class="pill active">검색: “{{ $q }}”</span> @endif
        @if($role) <span class="pill active">포지션: {{ $role }}</span> @endif
        @if($teamId) <span class="pill active">팀: {{ optional($teams->firstWhere('id', $teamId))->name }}</span> @endif
        @if($active) <span class="pill active">활동 중</span> @endif
        @if($q || $role || $teamId || $active)
          <a class="pill text-decoration-none" href="{{ route('players.index') }}"><i class="fa fa-times me-1"></i>필터
            초기화</a>
        @endif
      </div>
    </form>

    @if($players->count() === 0)
      <div class="empty text-center text-muted">
        <i class="fa fa-users fa-2x mb-2"></i>
        <div class="mb-1 fw-semibold">조건에 맞는 선수가 없습니다.</div>
        <div class="small">검색어와 필터를 바꿔 보세요.</div>
        <div class="mt-3">
          <a href="{{ route('players.create') }}" class="btn btn-outline-primary btn-sm">
            <i class="fa fa-user-plus me-1"></i>새 선수 등록
          </a>
        </div>
      </div>
    @else

      <div class="row players-grid row-cols-2 row-cols-md-3 row-cols-lg-4">
        @foreach($players as $player)
          <div class="col">
            <div class="card players-card">
              <div class="thumb-wrap player-thumb position-relative">
                @php
                  $src = $player->photo_src;
                @endphp

                @if($src)
                  <img src="{{ $src }}" class="players-thumb-img" alt="{{ $player->ign }}">
                @else
                  <div class="players-thumb-fallback">
                    <i class="fa fa-user-circle fa-2x"></i>
                  </div>
                @endif

                <a href="{{ route('players.show', $player) }}" class="stretched-link" aria-label="{{ $player->ign }} 상세보기"
                  title="{{ $player->ign }} 상세보기"></a>
              </div>

              <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                  <div>
                    <h6 class="card-title mb-1">{{ $player->ign }}</h6>
                    <div class="text-muted small realname">{{ $player->name }}</div>

                    <div class="text-muted small mt-1 d-flex align-items-center gap-2 meta-line">
                      <span class="meta-chip">{{ $player->role ?: '—' }}</span>
                      <span class="dot">•</span>
                      <span class="meta-chip team-chip" title="{{ optional($player->team)->name }}">
                        {{ optional($player->team)->name ?? '—' }}
                      </span>
                    </div>

                  </div>

                  <span class="badge state-pill {{ $player->is_active ? 'bg-success' : 'bg-secondary' }}">
                    {{ $player->is_active ? '활동' : '비활동' }}
                  </span>
                </div>
              </div>

            </div>
          </div>
        @endforeach
      </div>

      <div class="mt-3 d-flex justify-content-center">
        {{ $players->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
      </div>

    @endif
  </div>
@endsection
