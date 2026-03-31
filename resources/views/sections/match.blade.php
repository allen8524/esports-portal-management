{{-- resources/views/match.blade.php --}}
<section class="match-section set-bg" data-setbg="{{ asset('img/match/match-bg.jpg') }}">
  @php
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Carbon;

    // 컨트롤러가 넘겨주면 사용, 아니면 여기서 조회
    $upcoming = $upcoming ?? null;
    $recent   = $recent   ?? null;

    if (!$upcoming) {
      $upcoming = DB::table('matches as m')
        ->leftJoin('teams as t1','t1.id','=','m.team1_id')
        ->leftJoin('teams as t2','t2.id','=','m.team2_id')
        ->when(request('league'), fn($q,$lg)=>$q->where('m.league',$lg))
        ->when(request('stage'),  fn($q,$st)=>$q->where('m.stage', $st))
        ->where('m.status','scheduled')
        ->whereNotNull('m.start_at')
        ->orderBy('m.start_at')             // 가까운 일정부터
        ->limit(3)
        ->select([
          'm.id','m.start_at','m.title','m.best_of','m.stage','m.league',
          't1.name as team1_name','t1.logo_url as team1_logo',
          't2.name as team2_name','t2.logo_url as team2_logo',
        ])
        ->get();
    }

    if (!$recent) {
      $recent = DB::table('matches as m')
        ->leftJoin('teams as t1','t1.id','=','m.team1_id')
        ->leftJoin('teams as t2','t2.id','=','m.team2_id')
        ->when(request('league'), fn($q,$lg)=>$q->where('m.league',$lg))
        ->when(request('stage'),  fn($q,$st)=>$q->where('m.stage', $st))
        ->where('m.status','finished')
        ->whereNotNull('m.start_at')
        ->orderByDesc('m.start_at')         // 최근 결과부터
        ->limit(3)
        ->select([
          'm.id','m.start_at','m.team1_score','m.team2_score','m.best_of','m.stage','m.league',
          't1.name as team1_name','t1.logo_url as team1_logo',
          't2.name as team2_name','t2.logo_url as team2_logo',
        ])
        ->get();
    }

    $fmtDate = fn($dt) => Carbon::parse($dt)->format('j F Y'); // 예: 15 September 2019
    $logoL   = fn($url) => \App\Models\Team::resolveLogoUrl($url) ?: asset('img/match/tf-1.jpg'); // 좌측 기본이미지
    $logoR   = fn($url) => \App\Models\Team::resolveLogoUrl($url) ?: asset('img/match/tf-2.jpg'); // 우측 기본이미지
  @endphp

  <div class="container">
    <div class="row">
      {{-- Next Match --}}
      <div class="col-lg-6">
        <div class="ms-content">
          <h4>다음 경기</h4>

          @forelse($upcoming as $m)
            <div class="mc-table">
              <table>
                <tbody>
                  <tr>
                    <td class="left-team">
                      <div class="match-logo-box">
                        <img src="{{ $logoL($m->team1_logo) }}" alt="">
                      </div>
                      <h6 class="team-name">{{ $m->team1_name ?? 'Team 1' }}</h6>
                    </td>
                    <td class="mt-content">
                      {{-- 상단 설명: 리그 · 스테이지 · BoX --}}
                      <div class="mc-op">
                        {{ $m->league ?? 'League' }}
                        @if(!empty($m->stage)) · {{ $m->stage }} @endif
                        @if(!empty($m->best_of)) · Bo{{ $m->best_of }} @endif
                      </div>
                      <h4>
                        {{-- 필요하면 상세로 링크: matches/{id} --}}
                        <a href="{{ url('matches/'.$m->id) }}" class="text-reset text-decoration-none">VS</a>
                      </h4>
                      <div class="mc-op">{{ $fmtDate($m->start_at) }}</div>
                    </td>
                    <td class="right-team">
                      <div class="match-logo-box">
                        <img src="{{ $logoR($m->team2_logo) }}" alt="">
                      </div>
                      <h6 class="team-name">{{ $m->team2_name ?? 'Team 2' }}</h6>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          @empty
            <div class="text-muted small">예정된 경기가 없습니다.</div>
          @endforelse
        </div>
      </div>

      {{-- Recent Results --}}
      <div class="col-lg-6">
        <div class="ms-content">
          <h4>최근 경기</h4>

          @forelse($recent as $m)
            <div class="mc-table">
              <table>
                <tbody>
                  <tr>
                    <td class="left-team">
                      <div class="match-logo-box">
                        <img src="{{ $logoL($m->team1_logo) }}" alt="">
                      </div>
                      <h6>{{ $m->team1_name ?? 'Team 1' }}</h6>
                    </td>
                    <td class="mt-content">
                      <div class="mc-op">
                        {{ $m->league ?? 'League' }}
                        @if(!empty($m->stage)) · {{ $m->stage }} @endif
                        @if(!empty($m->best_of)) · Bo{{ $m->best_of }} @endif
                      </div>
                      <h4>
                        <a href="{{ url('matches/'.$m->id) }}" class="text-reset text-decoration-none">
                          {{ (int)$m->team1_score }} : {{ (int)$m->team2_score }}
                        </a>
                      </h4>
                      <div class="mc-op">{{ $fmtDate($m->start_at) }}</div>
                    </td>
                    <td class="right-team">
                      <div class="match-logo-box">
                        <img src="{{ $logoR($m->team2_logo) }}" alt="">
                      </div>
                      <h6>{{ $m->team2_name ?? 'Team 2' }}</h6>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          @empty
            <div class="text-muted small">최근 결과가 없습니다.</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</section>
