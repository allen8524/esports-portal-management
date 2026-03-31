@extends('layouts.main')
@section('title','뉴스 작성')
@section('body_class','news-create')

@section('content')
<div class="container py-4" style="max-width: 900px;">
  <h3 class="mb-3">뉴스 작성</h3>
  @if($errors->any())
    <div class="alert alert-danger"><strong>입력 오류</strong> — 항목을 확인하세요.</div>
  @endif
  <form method="post" action="{{ route('news.store') }}" enctype="multipart/form-data">
    @include('news._form')
  </form>
</div>
@endsection
