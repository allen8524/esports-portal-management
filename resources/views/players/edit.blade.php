@extends('layouts.main')

@section('title', $player->ign.' 수정')
@section('body_class','players-page')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/playerstyle.css') }}">
@endpush

@section('content')
<div class="container py-4">

  {{-- 상단 툴바 --}}
  <div class="toolbar d-flex justify-content-between align-items-center">
    <div class="page-title">
      <span class="dot"></span>
      <h3 class="mb-0">선수 수정</h3>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('players.show', $player) }}" class="btn btn-sm btn-outline-secondary">
        <i class="fa fa-user me-1"></i>상세
      </a>
      <a href="{{ route('players.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fa fa-list-ul me-1"></i>목록
      </a>
    </div>
  </div>

  @if (session('ok'))
    <div class="alert alert-success card-elev">{{ session('ok') }}</div>
  @endif

  @if ($errors->any())
    <div class="alert alert-danger card-elev">
      <strong>입력 오류가 있습니다.</strong>
      <ul class="mb-0">
        @foreach ($errors->all() as $e)
          <li>{{ $e }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @php
    // 현재 이미지 URL 계산
    $currentSrc = $player->photo_src;
  @endphp

  <div class="card card-elev">
    <div class="card-body">
      {{-- 업데이트 폼 --}}
      <form id="playerUpdateForm" method="post" action="{{ route('players.update', $player) }}" class="row g-3" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- 기본 정보 --}}
        <div class="col-12 section">
          <div class="section-title"><i class="fa fa-id-card"></i> 기본 정보</div>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">실명</label>
              <input name="name" value="{{ old('name', $player->name) }}" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">인게임 닉네임 (IGN)</label>
              <input id="ign" name="ign" value="{{ old('ign', $player->ign) }}" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">슬러그(URL 식별자)</label>
              <input id="slug" name="slug" value="{{ old('slug', $player->slug) }}" class="form-control" placeholder="예: faker" required>
              <div class="help mt-1">영문/숫자/하이픈만 권장</div>
            </div>
          </div>
        </div>

        {{-- 상세/소속 --}}
        <div class="col-12 section">
          <div class="section-title"><i class="fa fa-user-gear"></i> 상세/소속</div>
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label">포지션</label>
              <select name="role" class="form-select">
                <option value="">— 선택 —</option>
                @foreach (['Top','Jungle','Mid','ADC','Support'] as $r)
                  <option value="{{ $r }}" @selected(old('role', $player->role)===$r)>{{ $r }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">국가코드(ISO-2)</label>
              <input id="country" name="country" value="{{ old('country', $player->country) }}" class="form-control" maxlength="2" placeholder="KR">
            </div>
            <div class="col-md-3">
              <label class="form-label">생년월일</label>
              <input type="date" name="birthdate" value="{{ old('birthdate', optional($player->birthdate)->format('Y-m-d')) }}" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">소속 팀</label>
              <select name="team_id" class="form-select">
                <option value="">— 선택 안 함 —</option>
                @foreach($teams as $t)
                  <option value="{{ $t->id }}" {{ old('team_id', $player->team_id) == $t->id ? 'selected' : '' }}>
                    {{ $t->name }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>
        </div>

        {{-- 이미지 --}}
        <div class="col-12 section">
          <div class="section-title"><i class="fa fa-image"></i> 이미지</div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">사진 파일 (변경 시 선택)</label>
              <input id="photo" type="file" name="photo" accept="image/*" class="form-control">
              <div class="help mt-1">JPG/PNG, 5MB 이하 권장</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">사진 URL (선택)</label>
              <input id="photo_url" name="photo_url" value="{{ old('photo_url', $player->photo_url) }}" class="form-control" placeholder="https://.../photo.jpg">
            </div>
            <div class="col-12">
              <div class="preview-box">
                <div class="frame">
                  <img id="preview" alt="preview" src="{{ $currentSrc }}">
                </div>
                <div class="help">
                  현재 이미지가 표시됩니다. 파일을 선택하거나 URL을 입력하면 미리보기로 교체됩니다.
                  @if($currentSrc)
                    <div class="mt-1">
                      <a href="{{ $currentSrc }}" target="_blank">현재 원본 보기</a>
                    </div>
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- 기간/상태 --}}
        <div class="col-12 section">
          <div class="section-title"><i class="fa fa-calendar-days"></i> 기간/상태</div>
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label">가입일</label>
              <input type="date" name="joined_at" value="{{ old('joined_at', optional($player->joined_at)->format('Y-m-d')) }}" class="form-control">
            </div>
            <div class="col-md-3">
              <label class="form-label">탈퇴일</label>
              <input type="date" name="left_at" value="{{ old('left_at', optional($player->left_at)->format('Y-m-d')) }}" class="form-control">
            </div>
            <div class="col-md-6 d-flex align-items-end">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                       {{ old('is_active', $player->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">활동 중</label>
              </div>
            </div>
          </div>
        </div>

        {{-- 메타 --}}
        <div class="col-12 section">
          <div class="section-title"><i class="fa fa-brackets-curly"></i> 메타(JSON)</div>
          <textarea name="meta" rows="3" class="form-control" placeholder='{"twitter":"...", "twitch":"..."}'>{{ old('meta', $player->meta) }}</textarea>
          <div class="help mt-1">JSON 형식이어야 합니다.</div>
        </div>

        {{-- 저장 버튼은 폼 밖의 액션바에서 form 속성으로 제출 --}}
      </form>

      {{-- 액션 바: 저장 · 취소 · 삭제(DELETE) --}}
      <div class="actions d-flex flex-wrap gap-2 justify-content-between align-items-center mt-3">
        <div class="d-flex gap-2">
          <button class="btn btn-primary" form="playerUpdateForm">
            <i class="fa fa-save me-1"></i>저장
          </button>
          <a href="{{ route('players.index') }}" class="btn btn-outline-secondary">취소</a>
        </div>

        <form action="{{ route('players.destroy', $player) }}" method="post"
              onsubmit="return confirm('정말 삭제할까요? 이 작업은 되돌릴 수 없습니다.');" class="d-inline-block">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger">
            <i class="fa fa-trash me-1"></i>삭제
          </button>
        </form>
      </div>

    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // IGN -> slug 자동 보정 (slug 비어있을 때만)
  (function() {
    const ign = document.getElementById('ign');
    const slug = document.getElementById('slug');
    if (ign && slug) {
      ign.addEventListener('blur', () => {
        if (!slug.value) {
          slug.value = ign.value
            .toLowerCase().trim()
            .replace(/\s+/g, '-')
            .replace(/[^a-z0-9-]/g, '')
            .replace(/-+/g, '-');
        }
      });
    }
  })();

  // 국가코드 대문자 자동화
  (function() {
    const c = document.getElementById('country');
    if (!c) return;
    c.addEventListener('input', () => { c.value = c.value.toUpperCase().replace(/[^A-Z]/g,'').slice(0,2); });
    c.addEventListener('blur',  () => { c.value = c.value.toUpperCase().replace(/[^A-Z]/g,'').slice(0,2); });
  })();

  // 이미지 파일/URL 미리보기
  (function () {
    const fileInput = document.getElementById('photo');
    const urlInput  = document.getElementById('photo_url');
    const preview   = document.getElementById('preview');
    const setPreview = (src) => { if (preview) preview.src = src || ''; };

    if (fileInput) {
      fileInput.addEventListener('change', (e) => {
        const f = e.target.files?.[0];
        if (!f) return; // 선택 취소 시 기존 유지
        const reader = new FileReader();
        reader.onload = ev => setPreview(ev.target.result);
        reader.readAsDataURL(f);
      });
    }
    if (urlInput) {
      urlInput.addEventListener('input', (e) => {
        const v = e.target.value.trim();
        if (/^https?:\/\//i.test(v)) setPreview(v);
      });
    }
  })();
</script>
@endpush
