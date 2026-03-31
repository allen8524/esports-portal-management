@extends('layouts.main')

@section('title','경기 목록')
@section('body_class','matches-page')

@section('content')
  <div class="matches-index">
    {{-- 상단 헤더 --}}
    <div class="d-flex justify-content-between align-items-center page-head">
      <h3 class="m-0">경기 목록</h3>
      <a href="{{ route('matches.create') }}" class="btn btn-sm btn-primary">+ 새 경기</a>
    </div>

    @if(session('success'))
      <div class="alert alert-success py-2">{{ session('success') }}</div>
    @endif

    {{-- 필터 바 --}}
    <form method="get" class="filter-bar mb-2">
      <div class="row g-2 align-items-center flex-md-nowrap">
        <div class="col-12 col-md-3">
          <input name="q" value="{{ request('q') }}"
                 class="form-control form-control-sm"
                 placeholder="제목/리그/스테이지">
        </div>

        <div class="col-6 col-md-2">
          <select name="team_id" class="form-select form-select-sm">
            <option value="">팀 전체</option>
            @foreach($teams as $t)
              <option value="{{ $t->id }}" @selected(request('team_id') == $t->id)>{{ $t->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-6 col-md-2">
          <select name="status" class="form-select form-select-sm">
            <option value="">상태 전체</option>
            @foreach(['scheduled' => '예정', 'live' => '진행중', 'finished' => '종료', 'canceled' => '취소'] as $k => $v)
              <option value="{{ $k }}" @selected(request('status') == $k)>{{ $v }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-6 col-md-2">
          <input type="date" name="from" value="{{ request('from') }}"
                 class="form-control form-control-sm">
        </div>

        <div class="col-6 col-md-2">
          <input type="date" name="to" value="{{ request('to') }}"
                 class="form-control form-control-sm">
        </div>

        <div class="col-6 col-md-auto d-flex justify-content-md-end">
          <button class="btn btn-sm matches-search-btn" type="submit">
            검색
          </button>
        </div>
      </div>
    </form>

    @php
      $badge  = ['scheduled' => 'secondary', 'live' => 'danger', 'finished' => 'success', 'canceled' => 'dark'];
      $groups = collect($list->items())->groupBy(fn($m) => $m->start_at ? $m->start_at->format('Y-m-d') : '날짜 미정');
    @endphp

    @if($list->isEmpty())
      <div class="text-muted py-5 text-center">데이터가 없습니다.</div>
    @else
      @foreach($groups as $date => $items)
        <div class="date-sep fw-bold small mt-3 mb-2">{{ $date }}</div>

        @foreach($items as $m)
          @php
            $t1 = $m->team1;
            $t2 = $m->team2;

            $t1w = (int) ($m->team1_score ?? 0);
            $t2w = (int) ($m->team2_score ?? 0);
            $bo  = max(1, (int) ($m->best_of ?? 1));

            $winnerId = optional($m->winner)->id ?? null;
            $t1Win = $winnerId ? ($t1 && $t1->id === $winnerId) : ($t1w > $t2w);
            $t2Win = $winnerId ? ($t2 && $t2->id === $winnerId) : ($t2w > $t1w);

            $statusKey = $m->status ?? 'scheduled';
            $needWins  = max(1, intdiv($bo, 2) + 1);
            $timeLabel = $m->start_at?->format('H:i') ?? '시간 미정';

            $t1Logo = $t1?->logo_src ?: null;
            $t2Logo = $t2?->logo_src ?: null;
          @endphp

          <div class="card match-row mb-2">
            <div class="card-body">
              {{-- 카드 상단: 메타 + 액션 --}}
              <div class="match-top d-flex justify-content-between align-items-center">
                <div class="match-meta d-flex align-items-center">
                  <span class="time-label">{{ $timeLabel }}</span>

                  @if($m->league)
                    <span class="chip">{{ $m->league }}</span>
                  @endif

                  @if($m->stage)
                    <span class="chip chip-soft">{{ $m->stage }}</span>
                  @endif

                  <span class="chip chip-bo">BO{{ $bo }}</span>

                  <span class="badge state-pill bg-{{ $badge[$statusKey] ?? 'secondary' }}">
                    @if($statusKey === 'live')
                      <span class="pulse-dot"></span>LIVE
                    @elseif($statusKey === 'scheduled')
                      예정
                    @elseif($statusKey === 'finished')
                      종료
                    @elseif($statusKey === 'canceled')
                      취소
                    @else
                      상태
                    @endif
                  </span>
                </div>

                <div class="match-actions d-flex gap-1">
                  <a class="btn btn-outline-secondary btn-sm" href="{{ route('matches.show', $m) }}">보기</a>
                  <a class="btn btn-outline-warning btn-sm" href="{{ route('matches.edit', $m) }}">수정</a>
                </div>
              </div>

              {{-- 카드 메인: 팀 vs 팀 --}}
              <div class="match-main row align-items-center g-2">
                {{-- 왼쪽 팀 --}}
                <div class="col-12 col-lg-5 team-cell">
                  <div class="team-side">
                    <div class="team-line">
                      @if($t1Logo)
                        <img class="team-logo" src="{{ $t1Logo }}" alt="{{ $t1?->name }} logo">
                      @else
                        <div class="team-logo placeholder" aria-hidden="true"></div>
                      @endif
                      <div class="min-w-0">
                        <div class="team-name text-truncate {{ $t1Win ? 'is-winner' : 'is-loser' }}">
                          {{ $t1?->name ?? 'TBD' }}
                        </div>
                        <div class="team-sub text-truncate">
                          {{ $t1?->region ?? '—' }}
                        </div>
                      </div>
                    </div>
                    <div class="pips">
                      @for($i = 1; $i <= $needWins; $i++)
                        <span class="pip {{ $i <= min($t1w, $needWins) ? 'on' : '' }}"></span>
                      @endfor
                    </div>
                  </div>
                </div>

                {{-- 가운데 점수/VS --}}
                <div class="col-12 col-lg-2 score-col">
                  @if($statusKey === 'scheduled')
                    <div class="score-vs">VS</div>
                    <div class="score-sub">예정 경기</div>
                  @else
                    <div class="score-main">
                      <span>{{ $t1w }}</span>
                      <span class="sep">:</span>
                      <span>{{ $t2w }}</span>
                    </div>
                    <div class="score-sub">
                      {{ $statusKey === 'live' ? '진행 중' : ($statusKey === 'finished' ? '종료' : '취소') }}
                    </div>
                  @endif
                </div>

                {{-- 오른쪽 팀 --}}
                <div class="col-12 col-lg-5 team-cell">
                  <div class="team-side align-items-end">
                    <div class="team-line justify-content-end">
                      <div class="min-w-0 text-end">
                        <div class="team-name text-truncate {{ $t2Win ? 'is-winner' : 'is-loser' }}">
                          {{ $t2?->name ?? 'TBD' }}
                        </div>
                        <div class="team-sub text-truncate">
                          {{ $t2?->region ?? '—' }}
                        </div>
                      </div>
                      @if($t2Logo)
                        <img class="team-logo" src="{{ $t2Logo }}" alt="{{ $t2?->name }} logo">
                      @else
                        <div class="team-logo placeholder" aria-hidden="true"></div>
                      @endif
                    </div>
                    <div class="pips justify-content-end">
                      @for($i = 1; $i <= $needWins; $i++)
                        <span class="pip {{ $i <= min($t2w, $needWins) ? 'on' : '' }}"></span>
                      @endfor
                    </div>
                  </div>
                </div>
              </div> {{-- /match-main --}}
            </div>
          </div>
        @endforeach
      @endforeach

      <div class="mt-3 d-flex justify-content-center">
        {{ $list->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
      </div>
    @endif
  </div>
@endsection
