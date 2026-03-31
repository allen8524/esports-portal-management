@extends('layouts.main')
@section('title','뉴스')
@section('body_class','news-page')

@section('content')
<div class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-3 page-head">
    <h3 class="mb-0">뉴스</h3>
    <a class="btn btn-sm btn-primary" href="{{ route('news.create') }}">
      <i class="fa fa-pen me-1"></i>글 쓰기
    </a>
  </div>

  {{-- 상단 필터/탭 --}}
  <form method="get" class="news-filter mb-3">
    <div class="row g-2 align-items-center">
      <div class="col-md-4">
        <div class="news-filter-inline">
          <label class="form-label mb-0">검색</label>
          <input name="q"
                 value="{{ $q }}"
                 class="form-control"
                 placeholder="제목/본문">
        </div>
      </div>

      <div class="col-md-3">
        <div class="news-filter-inline">
          <label class="form-label mb-0">카테고리</label>
          <select name="category" class="form-select">
            <option value="">전체</option>
            @foreach($categories as $c)
              <option value="{{ $c->slug }}" @selected($category===$c->slug)>{{ $c->name }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="col-md-3">
        <div class="news-filter-inline">
          <label class="form-label mb-0">정렬</label>
          <select name="sort" class="form-select">
            <option value="latest" @selected($sort==='latest')>최신순</option>
            <option value="popular" @selected($sort==='popular')>인기순</option>
          </select>
        </div>
      </div>

      <div class="col-md-2 text-end">
        <button class="btn btn-primary w-100">검색</button>
      </div>
    </div>
  </form>


  <div class="row row-cols-1 row-cols-md-3 g-3">
    @forelse($news as $n)
      @php
        $raw = (string)($n->excerpt ?? $n->content ?? '');
        $raw = preg_replace('/<(br|BR)\s*\/?>/i', "\n", $raw);
        $raw = preg_replace('/<\/(p|div|li|h[1-6]|blockquote|pre|tr)>/i', "\n", $raw);
        $plain = strip_tags($raw);
        $plain = preg_replace("/\n{3,}/", "\n\n", $plain);
        $excerpt = \Illuminate\Support\Str::limit(trim($plain), 180);
        $host = $n->source_url ? (parse_url($n->source_url, PHP_URL_HOST) ?: null) : null;
        $thumb = $n->cover_path ? asset('storage/'.$n->cover_path) : asset('img/news/default.jpg');
      @endphp

      <div class="col">
        <a href="{{ route('news.show',$n) }}" class="text-decoration-none text-reset">
          <div class="card news-card">
            <div class="thumb-wrap">
              <img src="{{ $thumb }}" alt="cover">
            </div>

            <div class="card-body">
              <div class="d-flex gap-2 align-items-center mb-1">
                @if($n->is_pinned)
                  <span class="badge badge-pin">고정</span>
                @endif
                @if($n->category)
                  <span class="badge bg-secondary">{{ $n->category->name }}</span>
                @endif
                @if($host)
                  <span class="badge badge-src">{{ $host }}</span>
                @endif
              </div>

              <h5 class="card-title mb-1">{{ $n->title }}</h5>
              <p class="news-excerpt mb-2">{{ $excerpt }}</p>

              <div class="small text-muted">
                {{ optional($n->published_at)->timezone('Asia/Seoul')->format('Y-m-d H:i') }}
                · 조회 {{ number_format($n->views) }}
              </div>
            </div>

            <div class="card-footer text-end">
              <span class="btn btn-sm btn-outline-primary">자세히 보기</span>
            </div>
          </div>
        </a>
      </div>
    @empty
      <div class="col">
        <div class="alert alert-light">게시물이 없습니다.</div>
      </div>
    @endforelse
  </div>

  <div class="mt-3 d-flex justify-content-center">
    {{ $news->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
  </div>
</div>
@endsection
