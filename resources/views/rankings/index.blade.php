{{-- resources/views/rankings/index.blade.php --}}
@extends('layouts.main')

@section('title', '팀 순위')
@section('body_class', 'rankings-page')

@section('content')
  @php
    $s = request()->input('sort', 'wins');
    $d = strtolower(request()->input('dir', 'desc')) === 'asc' ? 'asc' : 'desc';
    $league = trim($league ?? request('league', ''));
    $stage = trim($stage ?? request('stage', ''));
    $rows = $standings ?? collect();

    // 컨트롤러에서 넘어온 옵션 (없으면 빈 컬렉션)
    $leagueOptions = collect($leagueOptions ?? []);
    $stageOptions = collect($stageOptions ?? []);
  @endphp


  <div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
      {{-- 왼쪽: 제목 --}}
      <div class="page-head-title mb-0">
        팀 순위
      </div>

      {{-- 오른쪽: 상태 정보 칩들 --}}
      <div class="d-flex flex-wrap meta-chips justify-content-end">
        <span class="meta-chip">
          리그: {{ $league !== '' ? $league : '전체' }}
        </span>
        <span class="meta-chip">
          스테이지: {{ $stage !== '' ? $stage : '전체' }}
        </span>
        <span class="meta-chip">
          정렬: {{ strtoupper($s) }} · {{ $d === 'desc' ? '내림차순' : '오름차순' }}
        </span>
        @if(!request()->boolean('include_all'))
          <span class="meta-chip">경기 1경기 이상 팀만</span>
        @else
          <span class="meta-chip">경기 0팀 포함</span>
        @endif
      </div>
    </div>

    <div class="card filter-card mb-3">
      <div class="card-body py-3">
        <form method="get" action="{{ route('rankings.index') }}"
              class="row g-3 align-items-center flex-wrap">

          {{-- 리그 --}}
          <div class="col-auto">
            <div class="d-flex align-items-center filter-field">
              <span class="filter-label me-1">리그</span>
              <select name="league" class="form-select form-select-sm">
                <option value="">전체</option>
                @foreach($leagueOptions as $opt)
                  <option value="{{ $opt }}" {{ $league === $opt ? 'selected' : '' }}>
                    {{ $opt }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>

          {{-- 스테이지 --}}
          <div class="col-auto">
            <div class="d-flex align-items-center filter-field">
              <span class="filter-label me-1">스테이지</span>
              <select name="stage" class="form-select form-select-sm">
                <option value="">전체</option>
                @foreach($stageOptions as $opt)
                  <option value="{{ $opt }}" {{ $stage === $opt ? 'selected' : '' }}>
                    {{ $opt }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>

          {{-- 정렬 --}}
          <div class="col-auto">
            <div class="d-flex align-items-center filter-field">
              <span class="filter-label me-1">정렬</span>
              <select name="sort" class="form-select form-select-sm">
                <option value="wins"     {{ $s === 'wins' ? 'selected' : '' }}>승</option>
                <option value="winrate"  {{ $s === 'winrate' ? 'selected' : '' }}>승률</option>
                <option value="diff"     {{ $s === 'diff' ? 'selected' : '' }}>득실</option>
                <option value="sf"       {{ $s === 'sf' ? 'selected' : '' }}>득점</option>
                <option value="sa"       {{ $s === 'sa' ? 'selected' : '' }}>실점</option>
                <option value="name"     {{ $s === 'name' ? 'selected' : '' }}>팀명</option>
              </select>
            </div>
          </div>

          {{-- 방향 --}}
          <div class="col-auto">
            <div class="d-flex align-items-center filter-field">
              <span class="filter-label me-1">방향</span>
              <select name="dir" class="form-select form-select-sm">
                <option value="desc" {{ $d === 'desc' ? 'selected' : '' }}>내림차순</option>
                <option value="asc"  {{ $d === 'asc' ? 'selected' : '' }}>오름차순</option>
              </select>
            </div>
          </div>

          {{-- 경기 0팀 포함 --}}
          <div class="col-auto d-flex align-items-center">
            <div class="form-check form-switch mb-0">
              <input class="form-check-input" type="checkbox" role="switch" id="include_all"
                     name="include_all" value="1"
                     {{ request()->boolean('include_all') ? 'checked' : '' }}>
              <label class="form-check-label small ms-1" for="include_all">
                경기 0팀 포함
              </label>
            </div>
          </div>

          {{-- 검색 버튼 --}}
          <div class="col-auto d-flex align-items-center ms-auto">
            <button type="submit" class="btn btn-primary btn-sm px-4">
              검색
            </button>
          </div>

        </form>
      </div>
    </div>



    <div class="card rankings-card sticky-head">
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-sm table-hover align-middle">
            <colgroup>
              <col class="col-rank">
              <col class="col-team">
              <col class="col-wl">
              <col class="col-winrate">
              <col class="col-setwl">
              <col class="col-diff">
            </colgroup>
            <thead>
              <tr>
                <th class="text-center"></th>
                <th>팀</th>
                <th class="text-end">W-L</th>
                <th class="text-end">승률</th>
                <th class="text-end">세트 W-L</th>
                <th class="text-end">±</th>
              </tr>
            </thead>
            <tbody>
              @forelse($rows as $i => $row)
                @php
                  $rank = $i + 1;
                  $games = (int) ($row->games ?? 0);
                  $wins = (int) ($row->wins ?? 0);
                  $loss = (int) ($row->losses ?? 0);
                  $sf = (int) ($row->score_for ?? 0);
                  $sa = (int) ($row->score_against ?? 0);
                  $diff = isset($row->diff) ? (int) $row->diff : ($sf - $sa);
                  $wr = isset($row->win_rate) ? (float) $row->win_rate : ($games ? round($wins / max($games, 1) * 100, 1) : 0);
                  $name = $row->team_name ?? '팀';
                  $logo = \App\Models\Team::resolveLogoUrl($row->logo_url ?? $row->logo ?? null);
                  $teamId = $row->team_id ?? $row->id ?? null;
                @endphp
                <tr>
                  <td class="text-center">
                    <span class="rank-badge">{{ $rank }}</span>
                  </td>
                  <td class="fw-semibold">
                    @if($teamId)
                      <a href="{{ route('teams.show', $teamId) }}" class="team-link d-flex align-items-center gap-2">
                        @if($logo)
                          <img src="{{ $logo }}" alt="" class="team-logo">
                        @else
                          <span class="d-inline-block bg-light border rounded" style="width:28px;height:28px;"></span>
                        @endif
                        <span class="nowrap">{{ $name }}</span>
                      </a>
                    @else
                      <div class="d-flex align-items-center gap-2">
                        @if($logo)
                          <img src="{{ $logo }}" alt="" class="team-logo">
                        @else
                          <span class="d-inline-block bg-light border rounded" style="width:28px;height:28px;"></span>
                        @endif
                        <span class="nowrap">{{ $name }}</span>
                      </div>
                    @endif
                  </td>
                  <td class="text-end nowrap">
                    <span class="fw-semibold">{{ $wins }}</span><span class="text-muted">-</span><span>{{ $loss }}</span>
                  </td>
                  <td class="text-end winrate">{{ number_format($wr, 1) }}%</td>
                  <td class="text-end nowrap">
                    <span class="fw-semibold">{{ $sf }}</span><span class="text-muted">-</span><span>{{ $sa }}</span>
                  </td>
                  <td class="text-end">
                    @if($diff > 0)
                      <span class="pos">+{{ $diff }}</span>
                    @elseif($diff < 0)
                      <span class="neg">{{ $diff }}</span>
                    @else
                      <span class="text-muted">0</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center text-muted py-5">집계 데이터가 없습니다.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
@endsection
