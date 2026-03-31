@extends('layouts.main')

@section('title', '선수 등록')
@section('body_class', 'players-page')

@push('styles')
  <link rel="stylesheet" href="{{ asset('css/playerstyle.css') }}">
@endpush

@section('content')
  <div class="container py-4">

    {{-- 상단 툴바 --}}
    <div class="toolbar d-flex justify-content-between align-items-center">
      <div class="page-title">
        <span class="dot"></span>
        <h3 class="mb-0">선수 등록</h3>
      </div>
      <a href="{{ route('players.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fa fa-list-ul me-1"></i>목록
      </a>
    </div>

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

    <div class="card card-elev">
      <div class="card-body">
        <form method="post" action="{{ route('players.store') }}" class="row g-3" enctype="multipart/form-data">
          @csrf

          {{-- 기본 정보 --}}
          <div class="col-12 section">
            <div class="section-title"><i class="fa fa-id-card"></i> 기본 정보</div>
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">실명</label>
                <input name="name" value="{{ old('name') }}" class="form-control" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">인게임 닉네임 (IGN)</label>
                <input id="ign" name="ign" value="{{ old('ign') }}" class="form-control" required>
              </div>
              <div class="col-md-4">
                <label class="form-label">슬러그(URL 식별자)</label>
                <input id="slug" name="slug" value="{{ old('slug') }}" class="form-control" placeholder="예: faker"
                  required>
                <div class="help mt-1">영문/숫자/하이픈만 권장 (예: <code>faker</code>)</div>
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
                  @foreach (['Top', 'Jungle', 'Mid', 'ADC', 'Support'] as $r)
                    <option value="{{ $r }}" @selected(old('role') === $r)>{{ $r }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-2">
                <label class="form-label">국가코드(ISO-2)</label>
                <input name="country" value="{{ old('country', 'KR') }}" class="form-control" maxlength="2"
                  placeholder="KR">
              </div>
              <div class="col-md-3">
                <label class="form-label">생년월일</label>
                <input type="date" id="birthdate" name="birthdate" value="{{ old('birthdate') }}" class="form-control">
              </div>
              <div class="col-md-4">
                <label class="form-label">소속 팀</label>
                <select name="team_id" class="form-select">
                  <option value="">— 선택 안 함 —</option>
                  @foreach($teams as $t)
                    <option value="{{ $t->id }}" {{ old('team_id') == $t->id ? 'selected' : '' }}>
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
                <label class="form-label">사진 파일</label>
                <input id="photo" type="file" name="photo" accept="image/*" class="form-control">
                <div class="help mt-1">JPG/PNG, 5MB 이하 권장</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">사진 URL (선택)</label>
                <input id="photo_url" name="photo_url" value="{{ old('photo_url') }}" class="form-control"
                  placeholder="https://.../photo.jpg">
              </div>
              <div class="col-12">
                <div class="preview-box">
                  <div class="frame">
                    <img id="preview" alt="preview" />
                  </div>
                  <div class="help">사진 파일을 선택하거나 사진 URL을 입력하면 미리보기됩니다.</div>
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
                <input type="date" name="joined_at" value="{{ old('joined_at') }}" class="form-control">
              </div>
              <div class="col-md-3">
                <label class="form-label">탈퇴일</label>
                <input type="date" name="left_at" value="{{ old('left_at') }}" class="form-control">
              </div>
              <div class="col-md-6 d-flex align-items-end">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                  <label class="form-check-label" for="is_active">활동 중</label>
                </div>
              </div>
            </div>
          </div>

          {{-- 메타 --}}
          <div class="col-12 section">
            <div class="section-title"><i class="fa fa-brackets-curly"></i> 메타(JSON)</div>
            <textarea name="meta" rows="3" class="form-control"
              placeholder='{"twitter":"...", "twitch":"..."}'>{{ old('meta') }}</textarea>
          </div>

          <div class="col-12 actions">
            <button class="btn btn-primary">
              <i class="fa fa-save me-1"></i>저장
            </button>
            <a href="{{ route('players.index') }}" class="btn btn-outline-secondary">취소</a>
          </div>

        </form>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    (function () {
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

    (function () {
      const fileInput = document.getElementById('photo');
      const urlInput = document.getElementById('photo_url');
      const preview = document.getElementById('preview');
      const setPreview = (src) => { if (preview) preview.src = src || ''; };

      if (fileInput) {
        fileInput.addEventListener('change', (e) => {
          const f = e.target.files?.[0]; if (!f) return setPreview('');
          const reader = new FileReader();
          reader.onload = ev => setPreview(ev.target.result);
          reader.readAsDataURL(f);
        });
      }
      if (urlInput) {
        urlInput.addEventListener('input', (e) => {
          const v = e.target.value.trim();
          if (/^https?:\/\//i.test(v)) setPreview(v); else setPreview('');
        });
      }
    })();

    (function () {
      const birth = document.getElementById('birthdate');
      if (!birth) return;
      birth.addEventListener('input', function () {
        if (!this.value) return;
        const parts = this.value.split('-');
        if (!parts[0]) return;
        if (parts[0].length > 4) {
          parts[0] = parts[0].slice(0, 4);
          this.value = parts.join('-');
        }
      });
    })();
  </script>
@endpush