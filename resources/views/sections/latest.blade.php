@php
  use App\Models\News;

  if (!isset($featured) || !isset($latests)) {
    $base = News::with('category')->published();

    $featured = $featured ?? (clone $base)
      ->orderByDesc('is_pinned')
      ->orderByDesc('published_at')
      ->first();

    $latests = $latests ?? (clone $base)
      ->when($featured, fn($q) => $q->where('id', '!=', $featured->id))
      ->orderByDesc('published_at')
      ->limit(4)
      ->get();
  }
@endphp

<section class="latest-section" id="latest-news">
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <div class="section-title">
          <h3>최근 <span>뉴스</span></h3>
        </div>

        <div class="row">
          <div class="col-md-6">
            @if($featured)
              @php
                $cover = $featured->cover_path
                  ? asset('storage/' . $featured->cover_path)
                  : asset('img/news/latest-b.jpg');
                $cateName = $featured->category->name ?? 'News';
                $dateStr = optional($featured->published_at)->timezone('Asia/Seoul')->format('M d, Y');
              @endphp

              <div class="news-item left-news">
                <div class="ni-pic set-bg" data-setbg="{{ $cover }}" style="background-image:url('{{ $cover }}')">
                  <div class="ni-tag">{{ $cateName }}</div>
                </div>
                <div class="ni-text">
                  <h4>
                    <a href="{{ route('news.show', $featured) }}">
                      {{ $featured->title }}
                    </a>
                  </h4>
                  <ul>
                    <li><i class="fa fa-calendar"></i> {{ $dateStr }}</li>
                    <li><i class="fa fa-eye"></i> {{ number_format($featured->views) }} views</li>
                  </ul>
                  @if($featured->excerpt)
                    <p>{{ \Illuminate\Support\Str::limit(strip_tags($featured->excerpt), 140) }}</p>
                  @endif
                </div>
              </div>
            @else
              <div class="alert alert-light">표시할 최신 기사가 없습니다.</div>
            @endif
          </div>

          <div class="col-md-6">
            @forelse ($latests as $n)
              @php
                $thumb = $n->cover_path
                  ? asset('storage/' . $n->cover_path)
                  : asset('img/news/ln-1.jpg');
                $dateStr = optional($n->published_at)->timezone('Asia/Seoul')->format('M d, Y');
              @endphp
              <div class="news-item">
                <div class="ni-pic">
                  <img src="{{ $thumb }}" alt="">
                </div>
                <div class="ni-text">
                  <h5><a href="{{ route('news.show', $n) }}">{{ $n->title }}</a></h5>
                  <ul>
                    <li><i class="fa fa-calendar"></i> {{ $dateStr }}</li>
                    <li><i class="fa fa-eye"></i> {{ number_format($n->views) }}</li>
                  </ul>
                </div>
              </div>
            @empty
              <div class="alert alert-light">더 보여줄 기사가 없습니다.</div>
            @endforelse
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        @php
          use Illuminate\Support\Facades\DB;

          $league = request('league');
          $stage = request('stage');
          $limit = 10;

          $home = DB::table('matches as m')
            ->when($league, fn($q, $lg) => $q->where('m.league', $lg))
            ->when($stage, fn($q, $st) => $q->where('m.stage', $st))
            ->where('m.status', 'finished')
            ->selectRaw('m.team1_id as team_id,
                                   CASE WHEN m.team1_score > m.team2_score THEN 1 ELSE 0 END as win,
                                   CASE WHEN m.team1_score < m.team2_score THEN 1 ELSE 0 END as loss');

          $away = DB::table('matches as m')
            ->when($league, fn($q, $lg) => $q->where('m.league', $lg))
            ->when($stage, fn($q, $st) => $q->where('m.stage', $st))
            ->where('m.status', 'finished')
            ->selectRaw('m.team2_id as team_id,
                                   CASE WHEN m.team2_score > m.team1_score THEN 1 ELSE 0 END as win,
                                   CASE WHEN m.team2_score < m.team1_score THEN 1 ELSE 0 END as loss');

          $base = $home->unionAll($away);

          $agg = DB::query()->fromSub($base, 'x')
            ->selectRaw('team_id, COUNT(*) as games, SUM(win) as wins, SUM(loss) as losses')
            ->groupBy('team_id');

          $ranking = DB::table('teams')
            ->joinSub($agg, 'a', 'a.team_id', '=', 'teams.id')
            ->selectRaw('teams.id, teams.name, teams.logo_url, a.games, a.wins, a.losses')
            ->orderByDesc('a.wins')
            ->orderByDesc(DB::raw('a.wins - a.losses'))
            ->orderBy('teams.name')
            ->limit($limit)
            ->get();

          $viewAllUrl = route('rankings.index', array_filter([
            'league' => $league,
            'stage' => $stage,
          ]));
        @endphp

        <div class="section-title">
          <h3>팀 <span>순위</span></h3>
        </div>
        <div class="points-table">
          <table>
            <thead>
              <tr>
                <th class="th-o">등수</th>
                <th>팀</th>
                <th class="th-o">판</th>
                <th class="th-o">승</th>
                <th class="th-o">패</th>
                <th class="th-o">승점</th>
              </tr>
            </thead>
            <tbody>
              @forelse($ranking as $idx => $row)
                @php
                  $pos = $idx + 1;
                  $p = (int) $row->games;
                  $w = (int) $row->wins;
                  $l = (int) $row->losses;
                  $pts = $w;
                  $logo = \App\Models\Team::resolveLogoUrl($row->logo_url ?? null) ?: asset('img/flag/flag-1.jpg');
                @endphp
                <tr>
                  <td>{{ $pos }}</td>
                  <td class="team-name">
                    <a href="{{ route('teams.show', $row->id) }}"
                      class="d-inline-flex align-items-center gap-1 text-decoration-none text-reset">
                      <img src="{{ $logo }}" alt="">
                      <span>{{ $row->name }}</span>
                    </a>
                  </td>
                  <td>{{ $p }}</td>
                  <td>{{ $w }}</td>
                  <td>{{ $l }}</td>
                  <td>{{ $pts }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" style="text-align:center;opacity:.7;">데이터가 없습니다.</td>
                </tr>
              @endforelse
            </tbody>
          </table>

          <a href="{{ $viewAllUrl }}" class="p-all">전체 보기</a>
        </div>
      </div>
    </div>
  </div>
</section>
