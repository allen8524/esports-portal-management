@php
  use App\Models\News;
@endphp

{{-- 컨트롤러에서 $trending 안 넘겼을 때 안전 가드 --}}
@if (!isset($trending))
  @php
    $limit = $limit ?? 10;
    // 최근 14일 내 조회수 순 → 부족하면 전체 조회수 순으로 대체
    $trending = News::published()
      ->where('published_at','>=', now()->subDays(14))
      ->orderByDesc('views')
      ->limit($limit)
      ->get();

    if ($trending->isEmpty()) {
      $trending = News::published()->orderByDesc('views')->limit($limit)->get();
    }
  @endphp
@endif

<div class="trending-news-section">
  <div class="container">
    <div class="tn-title"><i class="fa fa-caret-right"></i> &nbsp;&nbsp;&nbsp;많이 본 뉴스 </div>

    <div class="news-slider owl-carousel">
      @forelse ($trending as $n)
        <div class="nt-item">
          <a href="{{ route('news.show', $n) }}" class="text-reset text-decoration-none">
            {{ $n->title }}
          </a>
        </div>
      @empty
        <div class="nt-item">표시할 트렌딩 뉴스가 없습니다.</div>
      @endforelse
    </div>
  </div>
</div>

@push('styles')
<style>
  /* 한 줄로 흘러가며 길면 … 처리 */
  .trending-news-section .nt-item{
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  }
</style>
@endpush

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function(){
    if (window.jQuery && $('.news-slider.owl-carousel').length) {
      $('.news-slider.owl-carousel').owlCarousel({
        items: 1,
        loop: true,
        autoplay: true,
        autoplayTimeout: 3500,
        autoplayHoverPause: true,
        smartSpeed: 500,
        dots: false,
        nav: false
      });
    }
  });
</script>
@endpush
