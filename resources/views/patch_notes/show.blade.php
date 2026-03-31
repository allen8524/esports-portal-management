{{-- resources/views/patch_notes/show.blade.php --}}
@extends('layouts.main')
@section('title', ($note->version ? 'v'.$note->version.' ' : '').$note->title.' | 패치노트')
@section('body_class','patch-note-show')

@section('content')
@php
  // 히어로 배경
  $hero = $note->hero_image ?: asset('img/patch/default.png');

  // 작성자: 배열/JSON/문자열 어떤 형태든 안전하게 처리
  $authors = $note->authors ?? null;
  if (is_string($authors)) {
    $decoded = json_decode($authors, true);
    $authors = $decoded ?? array_filter(array_map('trim', preg_split('/[,|]/', $authors)));
  }
  if (!is_array($authors)) $authors = [];

  // 하이라이트(이미지+캡션) 배열일 때만 노출
  $highlights = is_array($note->highlights ?? null) ? $note->highlights : [];

  // 본문: body_html > body(plain) 우선
  $hasHtmlBody = !empty($note->body_html);
  $hasTextBody = !$hasHtmlBody && !empty($note->body);
@endphp

<div class="container py-4">

  {{-- Hero --}}
  <div class="rounded mb-3" style="background-image:url('{{ $hero }}'); background-size:cover; background-position:center; height: 240px;"></div>

  {{-- Meta --}}
  <div class="d-flex flex-wrap align-items-center gap-2 text-muted small mb-1">
    <span class="badge bg-dark text-uppercase">{{ strtoupper($note->game ?? 'LOL') }}</span>
    @if($note->version)<span class="badge bg-secondary">v{{ $note->version }}</span>@endif
    @if($note->published_at)<span>{{ $note->published_at->format('Y-m-d') }}</span>@endif
    @if(count($authors))
      <span>· {{ implode(' · ', $authors) }}</span>
    @endif
  </div>

  <h2 class="mb-3">{{ $note->title }}</h2>

  {{-- 하이라이트 섹션 (선택) --}}
  @if(count($highlights))
    <div class="row g-3 mb-3">
      @foreach($highlights as $h)
        <div class="col-12 col-md-6 col-lg-4">
          <div class="card h-100 shadow-sm">
            @if(!empty($h['image']))
              <img src="{{ $h['image'] }}" class="card-img-top" alt="{{ $h['caption'] ?? 'highlight' }}">
            @endif
            @if(!empty($h['caption']))
              <div class="card-body py-2">
                <div class="small text-muted">{{ $h['caption'] }}</div>
              </div>
            @endif
          </div>
        </div>
      @endforeach
    </div>
  @endif

  {{-- 본문 --}}
  <article class="lol-body">
    @if($hasHtmlBody)
      {!! $note->body_html !!}
    @elseif($hasTextBody)
      {!! nl2br(e($note->body)) !!}
    @else
      <div class="alert alert-info">
        본문이 아직 등록되지 않았습니다.
        @if(!empty($note->canonical_url))
          공식 패치노트를 참고하세요:
          <a href="{{ $note->canonical_url }}" target="_blank" rel="noopener">공식 페이지 열기</a>
        @endif
      </div>
    @endif
  </article>

  {{-- 공식 링크 버튼 (있을 때만) --}}
  @if(!empty($note->canonical_url))
    <div class="mt-4">
      <a class="btn btn-sm btn-outline-secondary" href="{{ $note->canonical_url }}" target="_blank" rel="noopener">
        공식 패치노트 보기
      </a>
    </div>
  @endif

  <div class="mt-3">
    <a href="{{ route('patch-notes.index', ['game'=>$note->game]) }}" class="btn btn-sm btn-outline-secondary">← 목록으로</a>
  </div>
</div>

{{-- 가벼운 타이포그래피 보정 --}}
<style>
  .lol-body h2, .lol-body h3 { margin-top: 1.25rem; margin-bottom: .75rem; }
  .lol-body h4 { margin-top: 1rem; margin-bottom: .5rem; }
  .lol-body p { margin-bottom: .75rem; line-height: 1.7; }
  .lol-body ul, .lol-body ol { padding-left: 1.1rem; margin-bottom: .75rem; }
  .lol-body img { max-width: 100%; height: auto; border-radius: .5rem; }
  .lol-body table { width: 100%; margin: .75rem 0; }
  .lol-body th, .lol-body td { padding: .5rem; }
</style>
@endsection
