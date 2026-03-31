<section class="video-section">
  <div class="container">
    <div class="row"><div class="col-lg-12">
      <div class="section-title"><h3>실시간 <span>급상승</span></h3></div>
    </div></div>

    {{-- 영상 슬라이더 --}}
    <div class="row">
      <div class="video-slider owl-carousel">

        @php
          use App\Models\Fixture;

          // 최근 VOD 있는 경기 8개
          $videos = Fixture::with(['team1','team2'])
                    ->whereNotNull('vod_url')
                    ->latest('start_at')
                    ->take(8)
                    ->get();

          // YouTube 썸네일 추출
          function yt_thumb($url) {
              $u = parse_url($url ?? '');
              $host = $u['host'] ?? '';
              $path = $u['path'] ?? '';
              $query = $u['query'] ?? '';
              $vid = null;

              if (str_contains($host, 'youtu.be')) {
                  $vid = ltrim($path, '/');
              } elseif (str_contains($host, 'youtube')) {
                  parse_str($query, $q);
                  $vid = $q['v'] ?? null;
                  if (!$vid) {
                      $parts = array_values(array_filter(explode('/', $path)));
                      if (count($parts) >= 2 && in_array($parts[0], ['shorts','live','embed'])) $vid = $parts[1];
                  }
              }
              return $vid ? "https://i.ytimg.com/vi/{$vid}/hqdefault.jpg" : null;
          }
        @endphp

        @forelse ($videos as $m)
          @php
            $t1 = $m->team1?->name ?? 'TBD';
            $t2 = $m->team2?->name ?? 'TBD';
            $title = $m->title ?: "{$t1} vs {$t2}";
            $thumb = yt_thumb($m->vod_url) ?: asset('img/videos/default.jpg');
          @endphp

          <div class="col-lg-3">
            <div class="video-item set-bg" data-setbg="{{ $thumb }}">
              <a href="{{ $m->vod_url }}" class="play-btn video-popup">
                <img src="{{ asset('img/videos/play.png') }}" alt="">
              </a>
            </div>
          </div>
        @empty
          {{-- 데이터 없으면 샘플 4개 노출 --}}
          @foreach ([1,2,3,4] as $i)
            <div class="col-lg-3">
              <div class="video-item set-bg" data-setbg="{{ asset("img/videos/video-$i.jpg") }}">
                <div class="vi-title"><h5>Sample Video {{ $i }}</h5></div>
                <a href="https://www.youtube.com/watch?v=dhYOPzcsbGM" class="play-btn video-popup">
                  <img src="{{ asset('img/videos/play.png') }}" alt="">
                </a>
                <div class="vi-time">VOD</div>
              </div>
            </div>
          @endforeach
        @endforelse

      </div>
    </div>
  </div>
</section>

@push('scripts')
<script>
  // 배경 이미지 세팅
  document.querySelectorAll('.set-bg').forEach(el=>{
    const bg = el.getAttribute('data-setbg');
    if (bg) el.style.backgroundImage = `url(${bg})`;
  });

  // Owl Carousel (이미 전역 초기화 되어 있으면 생략 가능)
  if (window.jQuery && jQuery.fn.owlCarousel) {
    jQuery('.video-slider').owlCarousel({
      items: 4, margin: 20, loop: true, nav: true, dots: true, autoplay: true,
      responsive: {0:{items:1},576:{items:2},992:{items:3},1200:{items:4}}
    });
  }
</script>
@endpush
