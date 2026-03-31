{{-- matches/create.blade.php --}}
@extends('layouts.main')
@section('title','새 경기')
@section('body_class','matches-page')

@section('content')
  <div class="matches-index mx-auto py-4" style="max-width:980px">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3 class="m-0 page-title">새 경기</h3>
      <a href="{{ route('matches.index') }}" class="btn btn-sm btn-outline-light matches-top-back">
        목록
      </a>
    </div>

    <form method="post" action="{{ route('matches.store') }}">
      @include('matches._form', ['match'=>new \App\Models\Fixture(), 'teams'=>$teams])
    </form>
  </div>
@endsection
