@extends('layouts.main')

@section('title', $team->name . ' 상세')
@section('body_class', 'teams-page')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/playerstyle.css') }}">
@endpush

@section('content')
  <div class="container py-4">

    {{-- 헤더 / 액션 --}}
    <div class="team-show-head d-flex align-items-center mb-4">
      <div class="team-show-main d-flex align-items-center">
        @php
          $abbr = trim($team->name) !== '' ? mb_substr($team->name, 0, 2) : 'TM';
        @endphp

        @if($team->logo_src)
          <div class="team-avatar">
            <img src="{{ $team->logo_src }}" alt="{{ $team->name }} logo">
          </div>
        @else
          <div class="team-avatar team-avatar-fallback">
            {{ $abbr }}
          </div>
        @endif

        <div class="team-show-text">
          @if($team->region)
            <div class="team-region small">{{ $team->region }}</div>
          @endif
          <h3 class="mb-1 team-name">{{ $team->name }}</h3>
          <div class="team-meta small text-muted">
            등록 {{ $team->created_at->format('Y-m-d H:i') }}
          </div>
        </div>
      </div>

      <div class="page-head-actions ms-auto d-flex">
        <a href="{{ route('teams.index') }}" class="btn btn-sm btn-outline-secondary">
          <i class="fa fa-list-ul me-1"></i>목록
        </a>
        <a href="{{ route('teams.edit', $team) }}" class="btn btn-sm btn-primary">
          <i class="fa fa-pen me-1"></i>수정
        </a>
        <form method="post" action="{{ route('teams.destroy', $team) }}" onsubmit="return confirm('정말 삭제할까요?');"
          class="d-inline">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-sm btn-outline-danger">
            <i class="fa fa-trash me-1"></i>삭제
          </button>
        </form>
      </div>
    </div>


    {{-- 본문 카드들 --}}
    <div class="row g-3 mb-4 team-info-row">
      <div class="col-md-6">
        <div class="card card-elev h-100">
          <div class="card-header py-2 team-card-header">팀 정보</div>
          <div class="card-body">
            <table class="table table-sm mb-0 team-info-table">
              <tr>
                <th style="width:130px">팀명</th>
                <td>{{ $team->name }}</td>
              </tr>
              <tr>
                <th>슬러그</th>
                <td>{{ $team->slug }}</td>
              </tr>
              <tr>
                <th>지역/리그</th>
                <td>{{ $team->region ?? '—' }}</td>
              </tr>
              <tr>
                <th>창단일</th>
                <td>{{ $team->founded_at?->format('Y-m-d') ?? '—' }}</td>
              </tr>
              <tr>
                <th>상태</th>
                <td>
                  @if($team->is_active)
                    <span class="badge state-pill bg-success-soft">운영</span>
                  @else
                    <span class="badge state-pill bg-secondary-soft">비운영</span>
                  @endif
                </td>
              </tr>
              <tr>
                <th>등록일</th>
                <td>{{ $team->created_at->format('Y-m-d H:i') }}</td>
              </tr>
              <tr>
                <th>수정일</th>
                <td>{{ $team->updated_at->format('Y-m-d H:i') }}</td>
              </tr>
            </table>
          </div>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card card-elev h-100">
          <div class="card-header py-2 team-card-header">로고 미리보기</div>
          <div class="card-body d-flex align-items-center justify-content-center">
            @if($team->logo_src)
              <img src="{{ $team->logo_src }}" alt="{{ $team->name }} logo" class="team-logo-large">
            @else
              <div class="text-muted">등록된 로고가 없습니다.</div>
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- 소속 선수 --}}
    <h5 class="mt-4 mb-2 team-section-title">
      소속 선수 <span class="small text-muted">({{ $team->players->count() }}명)</span>
    </h5>

    @if($team->players->isEmpty())
      <div class="alert alert-light border team-empty-alert">
        소속 선수가 없습니다.
      </div>
    @else
      <div class="list-group team-player-list">
        @foreach($team->players as $p)
          <a class="list-group-item list-group-item-action d-flex align-items-center" href="{{ route('players.show', $p) }}">
            @if($p->photo_src)
              <img src="{{ $p->photo_src }}" class="rounded me-3 team-player-thumb" alt="{{ $p->ign }}">
            @else
              <div class="rounded me-3 bg-light team-player-thumb"></div>
            @endif

            <div>
              <div class="fw-semibold">{{ $p->ign }}</div>
              <div class="text-muted small">
                {{ $p->name }} · {{ $p->role ?? '—' }} · {{ $p->country ?? '—' }}
              </div>
            </div>

            <div class="ms-auto text-muted small join-date-pill">
              가입일 {{ $p->joined_at?->format('Y-m-d') ?? '—' }}
            </div>
          </a>
        @endforeach
      </div>
    @endif

  </div>
@endsection
