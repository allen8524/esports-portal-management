@extends('layouts.main')
@section('title', $item->title . ' | 뉴스')
@section('body_class','news-show')

@section('content')
@php
  $raw = (string)($item->content ?? '');
  $hasHtml = $raw !== strip_tags($raw);
  $host = $item->source_url ? (parse_url($item->source_url, PHP_URL_HOST) ?: null) : null;

  $textForRead = trim(strip_tags($raw));
  $chars = mb_strlen($textForRead, 'UTF-8');
  $readMin = max(1, (int)ceil($chars/700));
@endphp

<div class="container py-4" style="max-width: 880px;">
  <div class="card news-article border-0">
    <div class="card-body p-4 p-md-5">

      <header class="news-header mb-4">
        <div class="news-header-top d-flex justify-content-between align-items-start gap-3 mb-2">
          <h1 class="news-title mb-0">{{ $item->title }}</h1>

          <div class="news-actions d-flex flex-wrap gap-2">
            <a class="btn news-back-btn" href="{{ route('news.index') }}">← 목록</a>
            <a class="btn news-edit-btn" href="{{ route('news.edit',$item) }}">수정</a>
          </div>
        </div>

        <div class="news-meta d-flex flex-wrap align-items-center">
          <span>{{ optional($item->published_at)->timezone('Asia/Seoul')->format('Y-m-d H:i') }}</span>
          <span>조회 {{ number_format($item->views) }}</span>
          <span>약 {{ $readMin }}분 소요</span>

          @if($item->category)
            <span class="badge badge-cat">{{ $item->category->name }}</span>
          @endif

          @if($host)
            <span class="badge badge-src">{{ $host }}</span>
          @endif
        </div>

        @if($item->source_url)
          <div class="mt-3">
            <a class="btn btn-sm btn-outline-primary" href="{{ $item->source_url }}" target="_blank" rel="noopener">
              원문 보기
            </a>
          </div>
        @endif
      </header>

      @if($item->cover_path)
        <figure class="news-cover mb-4">
          <img src="{{ asset('storage/'.$item->cover_path) }}" class="img-fluid" alt="{{ $item->title }}">
        </figure>
      @endif

      <article class="news-content mb-4">
        @if($hasHtml)
          {!! $raw !!}
        @else
          {!! nl2br(e($raw)) !!}
        @endif
      </article>

      @if($item->source_url)
        <p class="small text-muted mb-0">
          출처:
          <a href="{{ $item->source_url }}" target="_blank" rel="noopener">
            {{ $host ?? $item->source_url }}
          </a>
        </p>
      @endif
    </div>
  </div>
</div>
@endsection
