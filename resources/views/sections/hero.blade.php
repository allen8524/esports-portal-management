{{-- resources/views/sections/here.blade.php --}}
@php
  // 컨트롤러에서 $nextMatch를 주면 그대로 사용, 없으면 여기서 1회 조회
  $next = $nextMatch ?? null;
  if (!$next) {
    $next = \Illuminate\Support\Facades\DB::table('matches as m')
      ->leftJoin('teams as t1','t1.id','=','m.team1_id')
      ->leftJoin('teams as t2','t2.id','=','m.team2_id')
      ->when(request('league'), fn($q,$lg)=>$q->where('m.league',$lg))
      ->when(request('stage'),  fn($q,$st)=>$q->where('m.stage', $st))
      ->where('m.status','scheduled')
      ->whereNotNull('m.start_at')
      ->orderBy('m.start_at')      // 가장 가까운 예정 경기
      ->select('m.*',
               't1.name as team1_name','t1.logo_url as team1_logo',
               't2.name as team2_name','t2.logo_url as team2_logo')
      ->first();
  }

  // 날짜 포맷팅
  $dt = $next ? \Illuminate\Support\Carbon::parse($next->start_at)->timezone(config('app.timezone')) : null;
  $when = $dt ? $dt->format('j F Y / H:i') . ' GMT' . $dt->format('O') : null;

  $matchUrl = $next ? url('matches/'.$next->id) : '#';
@endphp

<section class="hero-section set-bg hero-dim-70" data-setbg="{{ asset('img/hero/hero-1.gif') }}">
  <div class="container">
    <div class="row"><div class="col-lg-12">
      <div class="hs-item">
        <div class="container"><div class="row"><div class="col-lg-12">
          <div class="hs-text" style="text-shadow: 2px 1px 2px black;">
            @if($next)
              <h4 class="mb-2">{{ $when }}</h4>

              {{-- 타이틀이 있으면 우선, 없으면 팀명 VS 팀명 + 리그/스테이지 --}}
              @if(!empty($next->title))
                <h2 class="mb-3">{{ $next->title }}</h2>
              @else
                <h2 class="mb-1">
                  {{ $next->team1_name ?? 'Team 1' }}
                  <span style="color:red;">VS</span>
                  {{ $next->team2_name ?? 'Team 2' }}
                </h2>
                <div class="text-light opacity-75 mb-3">
                  {{ $next->league ?? 'League' }}
                  @if(!empty($next->stage)) · {{ $next->stage }} @endif
                  @if(!empty($next->best_of)) · Bo{{ $next->best_of }} @endif
                </div>
              @endif

              <a href="{{ $matchUrl }}" class="primary-btn">상세 정보</a>
            @else
              <h4 class="mb-2">No upcoming match</h4>
              <h2 class="mb-3">예정된 경기가 없습니다</h2>
              <a href="#" class="primary-btn disabled" tabindex="-1" aria-disabled="true">상세 정보</a>
            @endif
          </div>
        </div></div></div>
      </div>
    </div></div>
  </div>
</section>
