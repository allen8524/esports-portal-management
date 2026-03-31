@extends('layouts.main')

@section('title', trim(implode(' ', array_filter([optional($player->team)->name, $player->ign]))) . ' | 상세 정보')
@section('body_class', 'players-page')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/playerstyle.css') }}">
@endpush

@section('content')
  <div class="container py-4">
    @php
      // 이미지 URL
      $src = $player->photo_src;

      // 메타 파싱
      $meta = [];
      try {
        $meta = $player->meta ? json_decode($player->meta, true) ?: [] : [];
      } catch (\Throwable $e) {
        $meta = [];
      }

      // 날짜 포맷
      $fmtBirth  = optional($player->birthdate)?->format('Y-m-d');
      $fmtJoined = optional($player->joined_at)?->format('Y-m-d');
      $fmtLeft   = optional($player->left_at)?->format('Y-m-d');
    @endphp

    {{-- 상단 헤더: 팀명 / IGN / 버튼들 --}}
    <div class="player-head d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
      {{-- 왼쪽: 아바타 + 텍스트 --}}
      <div class="d-flex align-items-center gap-3">
        @if($src)
          <div class="player-head-avatar rounded-circle overflow-hidden"
               style="width:54px;height:54px;">
            <img src="{{ $src }}" class="w-100 h-100" style="object-fit:cover;" alt="{{ $player->ign }}">
          </div>
        @else
          <div class="player-head-avatar rounded-circle bg-light"
               style="width:54px;height:54px;"></div>
        @endif

        <div>
          <div class="text-muted small">
            {{ optional($player->team)->name ?: 'FA' }}
          </div>
          <h3 class="mb-0">{{ $player->ign }}</h3>
          <div class="text-muted small">{{ $player->name }}</div>
        </div>
      </div>

      {{-- 오른쪽: 목록 / 수정 버튼 --}}
      <div class="d-flex align-items-center gap-2">
        <a class="btn btn-sm btn-outline-secondary player-head-btn" href="{{ route('players.index') }}">
          <i class="fa fa-list-ul me-1"></i>목록
        </a>
        <a class="btn btn-sm btn-primary player-head-btn" href="{{ route('players.edit', $player) }}">
          <i class="fa fa-pen me-1"></i>수정
        </a>
      </div>
    </div>

    <div class="row g-4">
      {{-- 좌측: 이미지 카드 --}}
      <div class="col-md-5">
        <div class="card card-elev overflow-hidden">
          <div class="photo-hero">
            @if($src)
              <img src="{{ $src }}" alt="{{ $player->ign }}" class="img-fluid w-100">
            @else
              <div class="ratio ratio-4x3 bg-light d-flex align-items-center justify-content-center text-muted">
                NO IMAGE
              </div>
            @endif
          </div>

          <div class="card-body py-3">
            {{-- 라벨 줄: 왼쪽 배지, 오른쪽 역할칩(아이콘) --}}
            <div class="chips-row">
              <span class="badge state-pill {{ $player->is_active ? 'bg-success' : 'bg-secondary' }}">
                <i class="fa {{ $player->is_active ? 'fa-bolt' : 'fa-moon' }}"></i>
                {{ $player->is_active ? '활동' : '비활동' }}
              </span>

              <div class="push-right">
                @include('partials.role_chip_img', ['player' => $player])
              </div>
            </div>

            {{-- 원본 보기: 항상 오른쪽 정렬 --}}
            @if($src)
              <div class="mt-2 small text-end">
                <a href="{{ $src }}" target="_blank" class="text-muted d-inline-flex align-items-center orig-link">
                  <i class="fa fa-link me-1"></i>원본 보기
                </a>
              </div>
            @endif
          </div>
        </div>
      </div>

      {{-- 우측: 상세 정보 --}}
      <div class="col-md-7">
        <div class="card card-elev">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <div>
                <h5 class="mb-1">{{ $player->ign }}</h5>
                <div class="text-muted fw-bold">{{ $player->name }}</div>
              </div>
              <div class="text-end small text-muted">
                <div>등록: {{ $player->created_at }}</div>
                <div>수정: {{ $player->updated_at }}</div>
              </div>
            </div>

            <hr class="my-3">

            <div class="row g-3">
              <div class="col-6">
                <div class="kv">
                  <div class="k">포지션</div>
                  <div class="v">{{ $player->role ?? '—' }}</div>
                </div>
              </div>
              <div class="col-6">
                <div class="kv">
                  <div class="k">국가</div>
                  <div class="v">{{ $player->country ?? '—' }}</div>
                </div>
              </div>
              <div class="col-6">
                <div class="kv">
                  <div class="k">생년월일</div>
                  <div class="v">{{ $fmtBirth ?? '—' }}</div>
                </div>
              </div>
              <div class="col-6">
                <div class="kv">
                  <div class="k">소속 팀</div>
                  <div class="v">
                    @if($player->team)
                      <a href="{{ route('teams.show', $player->team) }}">{{ $player->team->name }}</a>
                    @else
                      —
                    @endif
                  </div>
                </div>
              </div>
              <div class="col-6">
                <div class="kv">
                  <div class="k">입단일</div>
                  <div class="v">{{ $fmtJoined ?? '—' }}</div>
                </div>
              </div>
              <div class="col-6">
                <div class="kv">
                  <div class="k">퇴단일</div>
                  <div class="v">{{ $fmtLeft ?? '—' }}</div>
                </div>
              </div>
            </div>

            {{-- 메타/소셜 --}}
            @if(!empty($meta))
              <hr class="my-3">
              <div class="section-title small"><i class="fa fa-brackets-curly"></i> 메타</div>
              <div class="meta-grid">
                @foreach($meta as $k => $v)
                  <div class="meta-item">
                    <div class="k">{{ $k }}</div>
                    <div class="v">
                      @if(is_string($v) && preg_match('#^https?://#', $v))
                        <a href="{{ $v }}" target="_blank">{{ $v }}</a>
                      @else
                        {{ is_scalar($v) ? $v : json_encode($v, JSON_UNESCAPED_UNICODE) }}
                      @endif
                    </div>
                  </div>
                @endforeach
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
