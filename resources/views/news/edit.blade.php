@extends('layouts.main')
@section('title','뉴스 수정')
@section('body_class','news-edit')

@section('content')
<div class="container py-4" style="max-width: 900px;">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">뉴스 수정</h3>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger"><strong>입력 오류</strong> — 항목을 확인하세요.</div>
  @endif

  <form method="post" action="{{ route('news.update',$item) }}" enctype="multipart/form-data">
    @method('PUT')
    @include('news._form')
  </form>

  {{-- 삭제용 숨김 폼: 버튼은 _form 안에서 form="news-delete-form"으로 사용 --}}
  <form id="news-delete-form" method="post" action="{{ route('news.destroy',$item) }}"
        class="d-none" onsubmit="return confirm('삭제할까요?');">
    @csrf
    @method('DELETE')
  </form>

</div>
@endsection
