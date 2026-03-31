@extends('layouts.main')
@section('title','팀 등록')
@section('body_class','teams-page')

@section('content')
<div class="container py-4">

  {{-- 상단 헤더 --}}
  <div class="page-head d-flex justify-content-between align-items-center mb-3">
    <div>
      <h3 class="mb-1 page-title">팀 등록</h3>
      <div class="d-flex gap-2 flex-wrap">
        <span class="meta"><i class="fa fa-circle-plus me-1"></i>새 팀 생성</span>
      </div>
    </div>
    <a href="{{ route('teams.index') }}" class="btn btn-sm btn-outline-secondary">
      <i class="fa fa-list-ul me-1"></i>목록
    </a>
  </div>

  {{-- 에러 --}}
  @if ($errors->any())
    <div class="alert alert-danger card-elev small">
      <strong class="d-block mb-1">입력 오류가 있습니다.</strong>
      <ul class="mb-0">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @php
    $abbr = trim(old('name','')) !== '' ? mb_substr(old('name'), 0, 2) : 'TM';
  @endphp

  <div class="card card-elev">
    <div class="card-body">
      <form method="post" action="{{ route('teams.store') }}" enctype="multipart/form-data" class="row g-4 form-wrap">
        @csrf

        {{-- 좌: 로고 미리보기/업로드 --}}
        <div class="col-lg-5">
          <div class="section-title"><i class="fa fa-image"></i> 로고</div>
          <div class="logo-preview">
            <div class="frame">
              <div class="logo-fallback" id="preview-fallback">{{ $abbr }}</div>
              <img id="preview" alt="logo" class="logo-img" style="display:none;">
            </div>
            <div class="mt-2">
              <label for="logo" class="form-label">로고 이미지</label>
              <input id="logo" type="file" name="logo" class="form-control" accept="image/*">
              <div class="help mt-1">JPG/PNG, 최대 2MB · 선택 시 위 미리보기 갱신</div>
            </div>
            <div class="drop-tip"><i class="fa fa-circle-info me-1"></i>정사각형/가로형 권장, 투명 PNG 가능</div>
          </div>
        </div>

        {{-- 우: 기본 정보 --}}
        <div class="col-lg-7">
          <div class="section-title"><i class="fa fa-id-card"></i> 기본 정보</div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">팀명</label>
              <input id="name" type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">슬러그 (선택)</label>
              <input id="slug" type="text" name="slug" class="form-control" value="{{ old('slug') }}" placeholder="미입력 시 자동 생성">
              <div class="kv-hint">영문/숫자/하이픈 권장, 공백은 자동 하이픈</div>
            </div>
            <div class="col-md-4">
              <label class="form-label">지역/리그</label>
              <input type="text" name="region" class="form-control" value="{{ old('region') }}" placeholder="LCK / LPL / LEC ...">
            </div>
            <div class="col-md-4">
              <label class="form-label">창단일</label>
              <input type="date" name="founded_at" class="form-control" value="{{ old('founded_at') }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active',1) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">활성 팀</label>
              </div>
            </div>
          </div>

          <div class="actions mt-4 d-flex gap-2 flex-wrap">
            <button class="btn btn-primary">
              <i class="fa fa-save me-1"></i>저장
            </button>
            <a class="btn btn-outline-secondary" href="{{ route('teams.index') }}">취소</a>
          </div>
        </div>

      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // 이름 -> 슬러그 자동 (slug 비어있을 때만)
  (function(){
    const name = document.getElementById('name');
    const slug = document.getElementById('slug');
    if(!name || !slug) return;
    name.addEventListener('blur', () => {
      if(slug.value.trim()) return;
      slug.value = name.value
        .toLowerCase().trim()
        .replace(/\s+/g, '-')
        .replace(/[^a-z0-9-]/g, '')
        .replace(/-+/g, '-');
    });
  })();

  // 로고 미리보기
  (function(){
    const input = document.getElementById('logo');
    const img = document.getElementById('preview');
    const fallback = document.getElementById('preview-fallback');
    if(!input || !img) return;

    input.addEventListener('change', (e) => {
      const f = e.target.files?.[0];
      if(!f) return;
      const reader = new FileReader();
      reader.onload = ev => {
        if(fallback) fallback.style.display = 'none';
        img.style.display = 'block';
        img.src = ev.target.result;
      };
      reader.readAsDataURL(f);
    });

    // 팀명 입력에 따라 약어도 즉시 갱신
    const name = document.getElementById('name');
    if(name && fallback){
      name.addEventListener('input', () => {
        const v = name.value.trim();
        fallback.textContent = v ? v.substring(0,2) : 'TM';
      });
    }
  })();
</script>
@endpush
