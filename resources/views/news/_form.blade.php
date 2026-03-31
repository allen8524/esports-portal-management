@csrf
<div class="row g-3 news-form">

  {{-- 제목 --}}
  <div class="col-12">
    <div class="news-field-inline">
      <label class="form-label mb-0">제목</label>
      <input name="title"
             value="{{ old('title', $item->title ?? '') }}"
             class="form-control"
             required>
    </div>
    @error('title')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  {{-- 카테고리 + 상단 고정 --}}
  <div class="col-12">
    <div class="d-flex align-items-center gap-3">
      <div class="flex-grow-1">
        <div class="news-field-inline">
          <label class="form-label mb-0">카테고리</label>
          <select name="category_id" class="form-select">
            <option value="">(없음)</option>
            @foreach($categories as $c)
              <option value="{{ $c->id }}"
                      @selected(old('category_id', $item->category_id ?? '') == $c->id)>
                {{ $c->name }}
              </option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="pinned-check form-check mb-0">
        <input type="checkbox" class="form-check-input" id="is_pinned" name="is_pinned" value="1"
               @checked(old('is_pinned', $item->is_pinned ?? false))>
        <label for="is_pinned" class="form-check-label">상단 고정</label>
      </div>
    </div>
  </div>

  {{-- 발행일시 --}}
  <div class="col-12">
    <div class="news-field-inline">
      <label class="form-label mb-0">발행일시</label>
      <input type="datetime-local"
             name="published_at"
             value="{{ old('published_at', $item->published_at ?? '') }}"
             class="form-control">
    </div>
    <div class="form-text mt-1">비우면 비공개(목록 미표시)</div>
  </div>

  {{-- 요약 --}}
  <div class="col-12">
    <div class="news-field-inline">
      <label class="form-label mb-0">요약(선택)</label>
      <input name="excerpt"
             value="{{ old('excerpt', $item->excerpt ?? '') }}"
             class="form-control">
    </div>
  </div>

  {{-- 본문 --}}
  <div class="col-12">
    <div class="news-field-inline">
      <label class="form-label mb-0">본문(HTML 허용)</label>
      <textarea name="content"
                rows="12"
                class="form-control"
                required>{{ old('content', $item->content ?? '') }}</textarea>
    </div>
    @error('content')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
  </div>

  {{-- 원문 링크 --}}
  <div class="col-12">
    <div class="news-field-inline">
      <label class="form-label mb-0">원문 링크(선택)</label>
      <input name="source_url"
             value="{{ old('source_url', $item->source_url ?? '') }}"
             class="form-control">
    </div>
  </div>

  {{-- 표지 이미지 --}}
  <div class="col-12">
    <div class="news-field-inline">
      <label class="form-label mb-0">표지 이미지(선택)</label>
      <input type="file" name="cover" class="form-control">
    </div>
    @if(!empty($item->cover_path))
      <div class="form-text mt-1">현재: {{ $item->cover_path }}</div>
    @endif
  </div>

  {{-- 버튼들 --}}
  <div class="col-12 d-flex justify-content-end align-items-center">
    <div class="d-flex align-items-center gap-2">
      @if(isset($item) && $item && $item->exists)
        <a href="{{ route('news.show', $item) }}"
           target="_blank"
           class="btn news-view-btn">
          보기
        </a>

        <button type="submit"
                class="btn news-delete-btn"
                form="news-delete-form">
          삭제
        </button>
      @endif

      <button type="submit" class="btn news-save-btn">
        저장
      </button>
    </div>
  </div>


</div>
