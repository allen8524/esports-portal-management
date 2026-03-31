@extends('layouts.main')

@section('title', '관리자 대시보드')
@section('body_class', 'admin-page')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/admin-dashboard.css') }}">
@endpush

@section('content')
  <div class="admin-shell">

    {{-- 헤더 영역 --}}
    <div class="admin-header">
      <div>
        <div class="admin-chip mb-2">
          <i class="fa fa-shield"></i> ADMIN PANEL
        </div>
        <div class="admin-title">관리자 대시보드</div>
        <div class="admin-subtitle">
          현재 사이트의 계정, 팀, 경기, 뉴스와 패치 노트 현황을 한 화면에서 확인할 수 있습니다.
        </div>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('admin.db.index') }}" class="btn btn-wine-ghost">
          <i class="fa fa-database me-1"></i> 전체 DB 보기
        </a>
        <a href="{{ route('matches.create') }}" class="btn btn-wine">
          <i class="fa fa-plus me-1"></i> 새 경기 등록
        </a>
      </div>
    </div>

    {{-- 1. 상단 요약 카드 --}}
    <div class="admin-summary-grid">
      <div class="admin-card">
        <div class="admin-card-label">
          <i class="fa fa-users"></i> 계정
        </div>
        <div class="admin-card-value">{{ $accountCount }}</div>
        <div class="admin-card-sub">관리자 {{ $adminCount }}명</div>
      </div>

      <div class="admin-card">
        <div class="admin-card-label">
          <i class="fa fa-shield"></i> 팀 &amp; 선수
        </div>
        <div class="admin-card-value">{{ $teamCount }}</div>
        <div class="admin-card-sub">선수 {{ $playerCount }}명 등록</div>
      </div>

      <div class="admin-card">
        <div class="admin-card-label">
          <i class="fa fa-trophy"></i> 경기
        </div>
        <div class="admin-card-value">{{ $totalMatches }}</div>
        <div class="admin-card-sub">
          예정 {{ $scheduledCount }} · 진행중 {{ $liveMatchCount }} · 종료 {{ $finishedCount }}
        </div>
      </div>

      <div class="admin-card">
        <div class="admin-card-label">
          <i class="fa fa-newspaper-o"></i> 콘텐츠
        </div>
        <div class="admin-card-value">{{ $newsCount }}</div>
        <div class="admin-card-sub">패치 노트 {{ $patchCount }}개</div>
      </div>
    </div>

    {{-- 2. 다가오는 경기 + 최근 뉴스 --}}
    <div class="row g-4 admin-section">
      <div class="col-lg-7">
        <div class="admin-section-title">
          <i class="fa fa-clock-o"></i> 다가오는 경기
        </div>
        <div class="admin-block">
          <div class="table-responsive">
            <table class="table admin-table">
              <thead>
                <tr>
                  <th class="w-140">시작 시간</th>
                  <th>매치업</th>
                  <th class="w-100">리그</th>
                  <th class="w-80">상태</th>
                </tr>
              </thead>
              <tbody>
                @forelse($upcomingMatches as $m)
                  @php
                    $t1 = $m->team1?->name ?? 'TBD';
                    $t2 = $m->team2?->name ?? 'TBD';
                    $badge = [
                      'scheduled' => 'secondary',
                      'live' => 'danger',
                      'finished' => 'success',
                      'canceled' => 'dark',
                    ];
                    $status = $m->status ?? 'scheduled';
                  @endphp
                  <tr class="admin-row-clickable" onclick="window.location='{{ route('matches.show', $m) }}'">
                    {{-- 시간 --}}
                    <td class="admin-col-time">
                      {{ optional($m->start_at)->format('m-d H:i') }}
                    </td>

                    {{-- 매치업 --}}
                    <td>
                      <div class="fw-semibold admin-match-main">
                        {{ $t1 }} <span class="text-soft">vs</span> {{ $t2 }}
                      </div>
                      @if($m->title)
                        <div class="text-soft text-xs">{{ $m->title }}</div>
                      @endif
                    </td>

                    <td>{{ $m->league ?? '-' }}</td>
                    <td>
                      <span class="badge badge-status bg-{{ $badge[$status] ?? 'secondary' }}">
                        {{ strtoupper($status) }}
                      </span>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-soft text-center py-3">
                      예정된 경기가 없습니다.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="admin-section-title">
          <i class="fa fa-newspaper-o"></i> 최근 뉴스
        </div>
        <div class="admin-block">
          <div class="p-3">
            @forelse($recentNews as $n)
              <div class="mb-3">
                <a href="{{ route('news.show', $n) }}" class="admin-link-strong d-block">
                  {{ $n->title }}
                </a>
                <div class="text-soft text-xs">
                  {{ optional($n->published_at)->format('Y-m-d H:i') ?? '발행 예정' }}
                </div>
              </div>
            @empty
              <div class="text-soft text-sm">
                등록된 뉴스가 없습니다.
              </div>
            @endforelse

            <div class="mt-2">
              <a href="{{ route('news.index') }}" class="btn btn-wine-ghost btn-sm">
                전체 뉴스 관리
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- 3. 최근 가입 계정 (패치 노트 섹션 제거, 풀폭으로 변경) --}}
    <div class="row g-4 admin-section">
      <div class="col-lg-12">
        <div class="admin-section-title">
          <i class="fa fa-user-circle-o"></i> 최근 가입 계정
        </div>
        <div class="admin-block">
          <div class="table-responsive">
            <table class="table admin-table">
              <thead>
                <tr>
                  <th>이름</th>
                  <th>이메일</th>
                  <th class="w-80">권한</th>
                  <th class="w-120">가입일</th>
                </tr>
              </thead>
              <tbody>
                @forelse($recentAccounts as $a)
                  <tr>
                    <td>{{ $a->name }}</td>
                    <td>{{ $a->email }}</td>
                    <td>
                      @if($a->is_admin)
                        <span class="badge badge-role-admin">ADMIN</span>
                      @else
                        <span class="badge badge-role-user">USER</span>
                      @endif
                    </td>
                    <td>{{ optional($a->created_at)->format('Y-m-d') }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-soft text-center py-3">
                      아직 가입한 계정이 없습니다.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    {{-- 4. 리그별 경기 수 + 최근 종료 경기 --}}
    <div class="row g-4 admin-section">
      <div class="col-lg-4">
        <div class="admin-section-title">
          <i class="fa fa-sitemap"></i> 리그별 경기 수 (상위 5개)
        </div>
        <div class="admin-block">
          <div class="table-responsive">
            <table class="table admin-table">
              <thead>
                <tr>
                  <th>리그</th>
                  <th class="text-end w-80">경기수</th>
                </tr>
              </thead>
              <tbody>
                @forelse($matchByLeague as $row)
                  <tr>
                    <td>{{ $row->league ?: '-' }}</td>
                    <td class="text-end">{{ $row->cnt }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="2" class="text-soft text-center py-3">
                      경기 데이터가 없습니다.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="col-lg-8">
        <div class="admin-section-title">
          <i class="fa fa-flag-checkered"></i> 최근 종료된 경기
        </div>
        <div class="admin-block">
          <div class="table-responsive">
            <table class="table admin-table">
              <thead>
                <tr>
                  <th class="w-140">경기 시간</th>
                  <th>매치업</th>
                  <th class="text-center w-100">스코어</th>
                  <th class="w-120">승리팀</th>
                </tr>
              </thead>
              <tbody>
                @forelse($recentMatches as $m)
                  @php
                    $t1 = $m->team1?->name ?? 'TBD';
                    $t2 = $m->team2?->name ?? 'TBD';
                    $winner = $m->winner?->name;
                  @endphp
                  <tr class="admin-row-clickable" onclick="window.location='{{ route('matches.show', $m) }}'">

                    {{-- 경기 시간: 줄바꿈 금지 --}}
                    <td class="admin-col-time">
                      {{ optional($m->start_at)->format('m-d H:i') }}
                    </td>

                    {{-- 매치업: 한 줄 + 길면 … 처리 --}}
                    <td>
                      <div class="admin-match-main">
                        {{ $t1 }} <span class="text-soft">vs</span> {{ $t2 }}
                      </div>
                    </td>

                    {{-- 스코어 한 줄 고정 --}}
                    <td class="text-center admin-score-cell">
                      {{ $m->team1_score }} : {{ $m->team2_score }}
                    </td>

                    {{-- 승리팀 한 줄 고정 --}}
                    <td class="admin-col-winner">
                      {{ $winner ?? '-' }}
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-soft text-center py-3">
                      종료된 경기가 없습니다.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

  </div>
@endsection