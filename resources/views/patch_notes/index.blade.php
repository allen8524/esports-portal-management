@extends('layouts.main')
@section('title','Patch Notes')
@section('body_class','patch-notes-page')

@section('content')
<div class="container py-4">
  <div class="row">
    <div class="col-lg-12 p-0">
      <div class="section-title"><h3>Patch <span>Notes</span></h3></div>
    </div>
  </div>

  <div class="row patch-row">
    @forelse ($notes as $n)
      <div class="col-lg-3 col-sm-6 p-0">
        <div class="patch-item set-bg"
             data-setbg="{{ $n->hero_image ?: asset('img/patch/default.jpg') }}">
          <div class="si-tag">
            {{ strtoupper($n->game ?? 'LOL') }}{{ $n->version ? ' · v'.$n->version : '' }}
          </div>
          <div class="si-text">
            <h5>
              <a href="{{ route('patch-notes.show', $n) }}">
                {{ \Illuminate\Support\Str::limit($n->title, 40) }}
              </a>
            </h5>
            <ul>
              <li><i class="fa fa-calendar"></i> {{ optional($n->published_at)->format('Y-m-d') }}</li>
            </ul>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="alert alert-light border">등록된 패치노트가 없습니다.</div>
      </div>
    @endforelse
  </div>

  <div class="mt-3">
    {{ $notes->links() }}
  </div>
</div>
@endsection
