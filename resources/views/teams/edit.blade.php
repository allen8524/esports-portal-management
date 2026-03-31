@extends('layouts.main')
@section('title', '팀 수정')
@section('body_class', 'teams-page')

@section('content')
<div class="container py-4">

  {{-- 상단 툴바 --}}
  <div class="page-head d-flex justify-content-between align-items-center mb-3">
    <div>
      <h3 class="mb-1 page-title">팀 수정</h3>
      <div class="d-flex gap-2 flex-wrap">
        <span class="meta"><i class="fa fa-clock me-1"></i>등록 {{ optional($team->created_at)->format('Y-m-d') }}</span>
        <span class="meta"><i class="fa fa-pen me-1"></i>수정 {{ optional($team->updated_at)->format('Y-m-d') }}</span>
      </div>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('teams.show', $team) }}" class="btn btn-sm btn-outline-secondary">
        <i class="fa fa-user-group me-1"></i>상세
      </a>
      <a href="{{ route('teams.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fa fa-list-ul me-1"></i>목록
      </a>
    </div>
  </div>

  {{-- Alerts --}}
  @if (session('success'))
    <div class="alert alert-success card-elev">{{ session('success') }}</div>
  @endif
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
    $logoSrc = $team->logo_src;
    $abbr = trim($team->name) !== '' ? mb_substr($team->name, 0, 2) : 'TM';
  @endphp

  <div class="card card-elev">
    <div class="card-body">

      {{-- 🔵 업데이트 폼 시작 --}}
      <form method="post" action="{{ route('teams.update', $team) }}" enctype="multipart/form-data" class="row g-4 form-wrap">
        @csrf
        @method('PUT')

        {{-- 좌: 로고 업로드 --}}
        <div class="col-lg-5">
          <div class="section-title"><i class="fa fa-image"></i> 로고</div>
          <div class="logo-preview">
            <div class="frame">
              @if($logoSrc)
                <img id="preview" src="{{ $logoSrc }}" alt="logo">
              @else
                <div class="logo-fallback" id="preview-fallback">{{ $abbr }}</div>
                <img id="preview" alt="logo" style="display:none;">
              @endif
            </div>
            <div>
              <label class="form-label">로고 이미지 (변경 시 선택)</label>
              <input id="logo" type="file" name="logo" class="form-control" accept="image/*">
              <div class="help mt-1">JPG/PNG, 최대 2MB. 선택하면 즉시 미리보기 적용</div>
            </div>

            @if($logoSrc)
              <div class="mt-1 small">
                <a href="{{ $logoSrc }}" target="_blank" class="text-muted">
                  <i class="fa fa-link me-1"></i>현재 원본 보기
                </a>
              </div>
            @endif

            <div class="drop-tip"><i class="fa fa-circle-info me-1"></i>정사각형 또는 가로형 권장</div>
          </div>
        </div>

        {{-- 우: 팀 정보 입력 --}}
        <div class="col-lg-7">
          <div class="section-title"><i class="fa fa-id-card"></i> 기본 정보</div>
          <div class="row g-3">

            <div class="col-md-6">
              <label class="form-label">팀명</label>
              <input type="text" name="name" class="form-control" value="{{ old('name', $team->name) }}" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">슬러그 (선택)</label>
              <input type="text" name="slug" id="slug" class="form-control"
                     value="{{ old('slug', $team->slug) }}" placeholder="예: t1">
              <div class="kv-hint">영문/숫자/하이픈 권장</div>
            </div>

            <div class="col-md-4">
              <label class="form-label">지역/리그</label>
              <input type="text" name="region" class="form-control"
                     value="{{ old('region', $team->region) }}" placeholder="KR / LPL / LEC ...">
            </div>

            <div class="col-md-4">
              <label class="form-label">창단일</label>
              <input type="date" name="founded_at" class="form-control"
                     value="{{ old('founded_at', optional($team->founded_at)->format('Y-m-d')) }}">
            </div>

            <div class="col-md-4 d-flex align-items-end">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                       {{ old('is_active', $team->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">활성 팀</label>
              </div>
            </div>

            <div class="col-12">
              <label class="form-label">현재 로고</label><br>
              @if($logoSrc)
                <img src="{{ $logoSrc }}" alt="logo" style="height:48px;object-fit:contain;">
              @else
                <span class="text-muted">등록된 로고 없음</span>
              @endif
            </div>

          </div>

          {{-- 🔵 업데이트 액션 버튼 --}}
          <div class="actions mt-4 d-flex gap-2 flex-wrap">
            <button class="btn btn-primary">
              <i class="fa fa-save me-1"></i>저장
            </button>
            <a href="{{ route('teams.show', $team) }}" class="btn btn-outline-secondary">취소</a>
          </div>

        </div>
      </form>
      {{-- 🔵 업데이트 폼 끝 --}}

      {{-- 🔴 삭제 폼: 업데이트 폼과 완전히 분리 --}}
      <form method="post" action="{{ route('teams.destroy', $team) }}"
            onsubmit="return confirm('정말 삭제할까요? 이 작업은 되돌릴 수 없습니다.');"
            class="mt-3 text-end">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-outline-danger danger">
          <i class="fa fa-trash me-1"></i>삭제
        </button>
      </form>

    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
  // 이름 → 슬러그 자동 변환
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
  })();
</script>
@endpush
