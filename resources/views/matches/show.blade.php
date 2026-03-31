@extends('layouts.main')

@section('title', ($match->title ?: (($match->team1?->name ?? 'TBD').' vs '.($match->team2?->name ?? 'TBD'))).' | 경기 상세')

@section('content')

@push('styles')
<style>

</style>
@endpush
@php
  $badge = ['scheduled'=>'secondary','live'=>'danger','finished'=>'success','canceled'=>'dark'];

  $t1 = $match->team1;
  $t2 = $match->team2;

  $t1w = (int)($match->team1_score ?? 0);
  $t2w = (int)($match->team2_score ?? 0);

  $bo  = max(1, (int)($match->best_of ?? 1));
  $needWins = max(1, intdiv($bo, 2) + 1); // BO5 -> 3, BO3 -> 2

  $winnerId = optional($match->winner)->id ?? null;
  $t1Win = $winnerId ? ($t1 && $t1->id === $winnerId) : ($t1w > $t2w);
  $t2Win = $winnerId ? ($t2 && $t2->id === $winnerId) : ($t2w > $t1w);

  $statusKey = $match->status ?? 'scheduled';

  // ---- VOD embed URL ----
  $embedUrl = null; $provider = null;
  if (!empty($match->vod_url)) {
      $vod = $match->vod_url;
      $u = parse_url($vod);
      $host = $u['host'] ?? '';
      $path = $u['path'] ?? '';
      $query = $u['query'] ?? '';
      if (str_contains($host, 'youtube') || str_contains($host, 'youtu.be')) {
          $vid = null;
          if (str_contains($host, 'youtu.be')) $vid = ltrim($path, '/');
          else { parse_str($query, $q); $vid = $q['v'] ?? null; if(!$vid){ $parts = array_values(array_filter(explode('/', $path))); if(count($parts)>=2 && in_array($parts[0], ['shorts','live','embed'])) $vid=$parts[1];}}
          if ($vid){ $vid = preg_replace('/[^A-Za-z0-9_\-]/', '', $vid); $embedUrl = "https://www.youtube.com/embed/{$vid}"; $provider = 'YouTube'; }
      } elseif (str_contains($host, 'vimeo.com')) {
          $parts = array_values(array_filter(explode('/', $path)));
          if (!empty($parts[0])) { $vid = preg_replace('/\D/', '', $parts[0]); if($vid){ $embedUrl = "https://player.vimeo.com/video/{$vid}"; $provider='Vimeo'; } }
      } elseif (str_contains($host, 'twitch.tv')) {
          $parent = request()->getHost();
          if (preg_match('#/videos/(\d+)#', $path, $m))      { $embedUrl = "https://player.twitch.tv/?video=v{$m[1]}&parent={$parent}&autoplay=false"; $provider='Twitch'; }
          elseif (preg_match('#/clip/([A-Za-z0-9_-]+)#', $path, $m)) { $embedUrl = "https://clips.twitch.tv/embed?clip={$m[1]}&parent={$parent}"; $provider='Twitch Clip'; }
          else { $parts = array_values(array_filter(explode('/', $path))); if(!empty($parts[0])){ $channel = $parts[0]; $embedUrl = "https://player.twitch.tv/?channel={$channel}&parent={$parent}&autoplay=false"; $provider='Twitch Live'; } }
      }
  }
@endphp

<div class="container py-4 match-show">

  {{-- 헤더 --}}
  <div class="page-head d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div class="d-flex align-items-center gap-2">
      <h3 class="mb-0 page-title">
        {{ $match->title ?: (($t1?->name ?? 'TBD').' vs '.($t2?->name ?? 'TBD')) }}
      </h3>
      <span class="chip chip-{{ $statusKey }}">
        @if($statusKey==='live')
          <span class="pulse-dot"></span> LIVE
        @elseif($statusKey==='finished')
          종료
        @elseif($statusKey==='canceled')
          취소
        @else
          예정
        @endif
      </span>
    </div>

    <div class="meta-wrap">
      @if($match->league)<span class="meta">{{ $match->league }}</span>@endif
      @if($match->stage)<span class="meta">{{ $match->stage }}</span>@endif
      @if($match->start_at)<span class="meta">{{ $match->start_at->format('Y-m-d H:i') }}</span>@endif
      <span class="meta bo">BO{{ $bo }}</span>
    </div>
  </div>

  {{-- 메인 카드 --}}
  <div class="match-card">
    <div class="teams">
      {{-- 팀1 --}}
      <div class="team team--left {{ $t1Win ? 'is-winner' : '' }}">
        <div class="team-inner">
          <div class="team-art"></div>
          <div class="team-body">
            <div class="logo-wrap">
              @if($t1?->logo_src)
                <img class="team-logo" src="{{ $t1->logo_src }}" alt="{{ $t1->name }} logo">
              @else
                <div class="team-logo placeholder"></div>
              @endif
            </div>
            <div class="team-info">
              <div class="name" title="{{ $t1?->name }}">{{ $t1?->name ?? 'TBD' }}</div>
              <div class="region">{{ $t1?->region ?? '—' }}</div>
              <div class="pips">
                @for($i=1; $i <= $needWins; $i++)
                  <span class="pip {{ $i <= min($t1w, $needWins) ? 'on' : '' }}"></span>
                @endfor
              </div>
            </div>
            <div class="score">{{ $t1w }}</div>
          </div>
        </div>
      </div>

      {{-- 센터 --}}
      <div class="center">
        @if($statusKey==='scheduled')
          <div class="start-at">{{ $match->start_at?->format('H:i') ?? '—' }}</div>
          <div class="vs-badge">VS</div>
        @else
          <div class="scoreboard">
            <span class="s">{{ $t1w }}</span>
            <span class="sep">:</span>
            <span class="s">{{ $t2w }}</span>
          </div>
          <div class="state-badge">
            <span class="badge bg-{{ $badge[$statusKey] ?? 'secondary' }}">
              {{ $statusKey === 'live' ? 'LIVE' : ($statusKey === 'finished' ? '종료' : ($statusKey === 'canceled' ? '취소' : '예정')) }}
            </span>
          </div>
        @endif
        <div class="bo-pill">Best of {{ $bo }}</div>
      </div>

      {{-- 팀2 --}}
      <div class="team team--right {{ $t2Win ? 'is-winner' : '' }}">
        <div class="team-inner">
          <div class="team-art"></div>
          <div class="team-body">
            <div class="score">{{ $t2w }}</div>
            <div class="team-info text-end">
              <div class="name" title="{{ $t2?->name }}">{{ $t2?->name ?? 'TBD' }}</div>
              <div class="region">{{ $t2?->region ?? '—' }}</div>
              <div class="pips">
                @for($i=1; $i <= $needWins; $i++)
                  <span class="pip {{ $i <= min($t2w, $needWins) ? 'on' : '' }}"></span>
                @endfor
              </div>
            </div>
            <div class="logo-wrap">
              @if($t2?->logo_src)
                <img class="team-logo" src="{{ $t2->logo_src }}" alt="{{ $t2->name }} logo">
              @else
                <div class="team-logo placeholder"></div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- 승자/노트 --}}
    @if($match->winner || $match->notes)
      <div class="extra">
        @if($match->winner)
          <div class="winner"><span class="label">승자</span> <strong>{{ $match->winner->name }}</strong></div>
        @endif
        @if($match->notes)
          <div class="notes">{{ $match->notes }}</div>
        @endif
      </div>
    @endif
  </div>

{{-- VOD (match-card 폭에 맞춤) --}}
@if($match->vod_url)
  <div class="match-card match-vod mt-3">
    <div class="match-vod__head d-flex justify-content-between align-items-center">
      <span class="small text-muted">VOD 미리보기 <span class="text-muted">({{ $provider ?: '링크' }})</span></span>
      <a href="{{ $match->vod_url }}" target="_blank" class="small link-secondary">원본 열기</a>
    </div>

    <div class="match-vod__body">
      @if($embedUrl)
        <div class="vod-aspect">
          <iframe
            src="{{ $embedUrl }}"
            title="VOD preview"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowfullscreen
            referrerpolicy="strict-origin-when-cross-origin"></iframe>
        </div>
      @else
        <div class="p-3 border rounded bg-white">
          <div class="text-muted small">이 링크는 자동 임베드가 지원되지 않습니다.</div>
          <a href="{{ $match->vod_url }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">원본 열기</a>
        </div>
      @endif
    </div>
  </div>
@endif



  {{-- 액션 --}}
  <div class="d-flex gap-2 mt-3">
    <a class="btn btn-outline-warning" href="{{ route('matches.edit',$match) }}"><i class="fa fa-pen me-1"></i>수정</a>
    <a class="btn btn-outline-secondary" href="{{ route('matches.index') }}"><i class="fa fa-list-ul me-1"></i>목록</a>
  </div>

</div>
@endsection
