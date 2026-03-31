@section('body_class', 'matches-page')

@csrf
@if ($errors->any())
  <div class="alert alert-danger">
    <ul class="mb-0 small">
      @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

@php
  $boSel = (int) old('best_of', $match->best_of ?? 3);
  $needWins = max(1, intdiv($boSel, 2) + 1); // BO5→3, BO3→2
@endphp

<div class="card mb-3">
  <div class="card-header py-2">매치 기본정보</div>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Team 1</label>
        <select name="team1_id" class="form-select" required>
          @foreach($teams as $t)
            <option value="{{ $t->id }}" @selected(old('team1_id', $match->team1_id ?? '') == $t->id)>{{ $t->name }}
            </option>
          @endforeach
        </select>
        <div class="form-text">왼쪽(홈) 팀</div>
      </div>

      <div class="col-md-6">
        <label class="form-label">Team 2</label>
        <select name="team2_id" class="form-select" required>
          @foreach($teams as $t)
            <option value="{{ $t->id }}" @selected(old('team2_id', $match->team2_id ?? '') == $t->id)>{{ $t->name }}
            </option>
          @endforeach
        </select>
        <div class="form-text">오른쪽(원정) 팀</div>
      </div>

      <div class="col-md-3">
        <label class="form-label">Best Of</label>
        <select name="best_of" class="form-select">
          @foreach([1, 3, 5, 7] as $bo)
            <option value="{{ $bo }}" @selected(old('best_of', $match->best_of ?? 3) == $bo)>BO{{ $bo }}</option>
          @endforeach
        </select>
        @php
          $boShow = (int) old('best_of', $match->best_of ?? 3);
          $needShow = max(1, intdiv($boShow, 2) + 1);
        @endphp
        <div class="form-text">
          필요 승수: <span class="badge rounded-pill bg-light text-dark">BO{{ $boShow }} → {{ $needShow }}</span>
          <span class="ms-1">
            @for($i = 1; $i <= $needShow; $i++) <span class="text-primary">●</span> @endfor
          </span>
        </div>
      </div>

      <div class="col-md-4">
        <label class="form-label">시작일시</label>
        <input type="datetime-local" name="start_at"
          value="{{ old('start_at', optional($match->start_at ?? null)->format('Y-m-d\TH:i')) }}" class="form-control">
        <div class="form-text">현지 시간 기준 입력</div>
      </div>

      <div class="col-md-5">
        <label class="form-label">상태</label>
        <select name="status" class="form-select">
          @foreach(['scheduled', 'live', 'finished', 'canceled'] as $s)
            <option value="{{ $s }}" @selected(old('status', $match->status ?? 'scheduled') == $s)>{{ $s }}</option>
          @endforeach
        </select>
        <div class="d-flex gap-1 mt-1">
          <span class="badge bg-secondary">scheduled</span>
          <span class="badge bg-danger">live</span>
          <span class="badge bg-success">finished</span>
          <span class="badge bg-dark">canceled</span>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card mb-3">
  <div class="card-header py-2">스코어 & 승자</div>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">스코어 (Team 1)</label>
        <input type="number" name="team1_score" min="0" value="{{ old('team1_score', $match->team1_score ?? 0) }}"
          class="form-control">
      </div>

      <div class="col-md-6">
        <label class="form-label">스코어 (Team 2)</label>
        <input type="number" name="team2_score" min="0" value="{{ old('team2_score', $match->team2_score ?? 0) }}"
          class="form-control">
      </div>

      <div class="col-md-6">
        <label class="form-label">승자(선택)</label>
        <select name="winner_team_id" class="form-select">
          <option value="">자동</option>
          @foreach($teams as $t)
            <option value="{{ $t->id }}" @selected(old('winner_team_id', $match->winner_team_id ?? '') == $t->id)>
              {{ $t->name }}</option>
          @endforeach
        </select>
        <div class="form-text">미선택 시 스코어를 기준으로 자동 결정</div>
      </div>

      <div class="col-md-6">
        <label class="form-label">리그 / 스테이지</label>

        <div class="row g-2">
          {{-- 리그: DB에 있는 값만 선택 가능 --}}
          <div class="col-md-6">
            <select name="league" class="form-select">
              <option value="">리그 선택</option>
              @foreach($leagues as $lg)
                <option value="{{ $lg }}" @selected(old('league', $match->league ?? '') == $lg)>
                  {{ $lg }}
                </option>
              @endforeach
            </select>
            <div class="form-text">예: LCK, LPL, LEC …</div>
          </div>

          {{-- 스테이지: 템플릿에서만 선택 가능 --}}
          <div class="col-md-6">
            <select name="stage" class="form-select">
              <option value="">스테이지 선택</option>
              @foreach($stages as $st)
                <option value="{{ $st }}" @selected(old('stage', $match->stage ?? '') == $st)>
                  {{ $st }}
                </option>
              @endforeach
            </select>
            <div class="form-text">예: Regional Split, MSI Bracket Stage, Worlds Finals</div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<div class="card mb-3">
  <div class="card-header py-2">메타 정보</div>
  <div class="card-body">
    <div class="row g-3">
      <div class="col-md-12">
        <label class="form-label">타이틀(옵션)</label>
        <input name="title" value="{{ old('title', $match->title ?? '') }}" class="form-control"
          placeholder="Grand Finals: AAA vs BBB">
        <div class="form-text">비워두면 ‘Team1 vs Team2’ 형식으로 표시</div>
      </div>

      <div class="col-md-6">
        <label class="form-label">VOD URL</label>
        <input name="vod_url" type="url" value="{{ old('vod_url', $match->vod_url ?? '') }}" class="form-control"
          placeholder="https://">
      </div>

      <div class="col-md-12">
        <label class="form-label">메모</label>
        <textarea name="notes" rows="3" class="form-control">{{ old('notes', $match->notes ?? '') }}</textarea>
      </div>
    </div>
  </div>
</div>

<div class="d-flex gap-2">
  {{-- 저장: 부모의 편집 폼을 제출 (부분뷰 내부에선 기본 submit이면 됨) --}}
  <button type="submit" class="btn btn-primary">저장</button>

  {{-- 삭제: 숨김 삭제 폼을 제출 (부모 뷰에 deleteMatchForm가 있어야 함) --}}
  @if(!empty($match?->id))
    <button type="submit" form="deleteMatchForm" class="btn btn-danger"
      onclick="return confirm('정말 삭제할까요? 삭제 후 되돌릴 수 없습니다.');">
      삭제
    </button>
  @endif

  <a class="btn btn-outline-secondary" href="{{ route('matches.index') }}">목록</a>
</div>