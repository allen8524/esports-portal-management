{{-- matches/edit.blade.php --}}
@extends('layouts.main')
@section('title','경기 수정')
@section('body_class','matches-page')

@section('content')
  <div class="matches-index mx-auto py-4" style="max-width:980px">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="m-0 page-title">경기 수정</h3>
      <a href="{{ route('matches.index') }}" class="btn btn-sm btn-outline-light matches-top-back">
        목록
      </a>
    </div>

    <form method="post" action="{{ route('matches.update', $match) }}">
      @csrf
      @method('PUT')
      @include('matches._form', ['match'=>$match, 'teams'=>$teams])
    </form>

    @if(!empty($match?->id))
      <form id="deleteMatchForm"
            method="post"
            action="{{ route('matches.destroy', $match) }}"
            class="d-none">
        @csrf
        @method('DELETE')
      </form>
    @endif
  </div>
@endsection
