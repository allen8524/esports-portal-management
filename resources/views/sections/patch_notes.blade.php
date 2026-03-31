<section class="soccer-section patch-section">
  <div class="container">
    <div class="row"><div class="col-lg-12 p-0">
      <div class="section-title"><h3>패치 <span>노트</span></h3></div>
    </div></div>

    <div class="row">
      @foreach(($patchNotes ?? \App\Models\PatchNote::published()->game('lol')->latest('published_at')->limit(4)->get()) as $n)
        @php
          // 1) canonical_url 우선
          $officialUrl = $n->canonical_url ?? null;

          // 2) 없으면 version→ slug (예: 25.22 -> patch-25-22-notes/)
          if (!$officialUrl && !empty($n->version)) {
            $officialUrl = 'https://www.leagueoflegends.com/ko-kr/news/game-updates/patch-'
                          . str_replace('.', '-', $n->version)
                          . '-notes/';
          }

          // 3) 그래도 없으면 기존 상세 라우트로 폴백
          if (!$officialUrl) {
            $officialUrl = route('patch-notes.show', $n);
          }
        @endphp

        <div class="col-lg-3 col-sm-6 p-0">
          <div class="soccer-item patch-item set-bg"
               data-setbg="{{ $n->hero_image ?: asset('img/patch/default.png') }}">
            <div class="si-tag">
              {{ strtoupper($n->game ?? 'LOL') }}{{ $n->version ? ' · v'.$n->version : '' }}
            </div>
            <div class="si-text">
              <h5>
                <a href="{{ $officialUrl }}" target="_blank" rel="noopener"
                   style="text-shadow: 2px 1px 2px black;">
                  {{ \Illuminate\Support\Str::limit($n->title, 42) }}
                </a>
              </h5>
              <ul>
                <li style="text-shadow: 2px 1px 2px black;">
                  <i class="fa fa-calendar"></i> {{ optional($n->published_at)->format('Y-m-d') }}
                </li>
              </ul>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>
