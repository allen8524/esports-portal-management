<section class="popular-section">
  <div class="container">
    @php
      $popularNews = $popularNews
        ?? \App\Models\News::whereNotNull('published_at')
              ->orderByDesc('is_pinned')
              ->orderByDesc('views')
              ->orderByDesc('published_at')
              ->limit(8)
              ->get();

      $mainLeft  = $popularNews->get(0);
      $mainRight = $popularNews->get(1);
      $leftList  = $popularNews->slice(2, 3);
      $rightList = $popularNews->slice(5, 3);

      $coverUrl = function ($news, $fallback) {
          if ($news && $news->cover_path) {
              return asset('storage/'.$news->cover_path);
          }
          return asset($fallback);
      };

      $votePlayers = $votePlayers
        ?? \App\Models\Player::inRandomOrder()
              ->limit(4)
              ->get();

      $voteTeams = $voteTeams
        ?? \App\Models\Team::inRandomOrder()
              ->limit(4)
              ->get();
    @endphp

    <div class="row">
      <div class="col-lg-8">
        <div class="section-title">
          <h3>인기 <span>뉴스</span></h3>
        </div>

        <div class="row">
          <div class="col-md-6">
            @if($mainLeft)
              <div class="news-item popular-item set-bg"
                   data-setbg="{{ $coverUrl($mainLeft, 'img/news/esports-main-1.jpg') }}">
                <div class="ni-text">
                  <h5>
                    <a href="{{ route('news.show', $mainLeft) }}">
                      {{ $mainLeft->title }}
                    </a>
                  </h5>
                  <ul>
                    <li>
                      <i class="fa fa-calendar"></i>
                      {{ $mainLeft->published_at ? substr($mainLeft->published_at, 0, 10) : '' }}
                    </li>
                    <li>
                      <i class="fa fa-eye"></i>
                      {{ number_format($mainLeft->views ?? 0) }} Views
                    </li>
                  </ul>
                </div>
              </div>
            @endif

            @foreach($leftList as $n)
              <div class="news-item">
                <div class="ni-pic">
                  <img src="{{ $coverUrl($n, 'img/news/default-thumb.jpg') }}" alt="">
                </div>
                <div class="ni-text">
                  <h5>
                    <a href="{{ route('news.show', $n) }}">
                      {{ $n->title }}
                    </a>
                  </h5>
                  <ul>
                    <li>
                      <i class="fa fa-calendar"></i>
                      {{ $n->published_at ? substr($n->published_at, 0, 10) : '' }}
                    </li>
                    <li>
                      <i class="fa fa-comment"></i>
                      0 Comments
                    </li>
                  </ul>
                </div>
              </div>
            @endforeach
          </div>

          <div class="col-md-6">
            @if($mainRight)
              <div class="news-item popular-item set-bg"
                   data-setbg="{{ $coverUrl($mainRight, 'img/news/esports-main-2.jpg') }}">
                <div class="ni-text">
                  <h5>
                    <a href="{{ route('news.show', $mainRight) }}">
                      {{ $mainRight->title }}
                    </a>
                  </h5>
                  <ul>
                    <li>
                      <i class="fa fa-calendar"></i>
                      {{ $mainRight->published_at ? substr($mainRight->published_at, 0, 10) : '' }}
                    </li>
                    <li>
                      <i class="fa fa-eye"></i>
                      {{ number_format($mainRight->views ?? 0) }} Views
                    </li>
                  </ul>
                </div>
              </div>
            @endif

            @foreach($rightList as $n)
              <div class="news-item">
                <div class="ni-pic">
                  <img src="{{ $coverUrl($n, 'img/news/default-thumb.jpg') }}" alt="">
                </div>
                <div class="ni-text">
                  <h5>
                    <a href="{{ route('news.show', $n) }}">
                      {{ $n->title }}
                    </a>
                  </h5>
                  <ul>
                    <li>
                      <i class="fa fa-calendar"></i>
                      {{ $n->published_at ? substr($n->published_at, 0, 10) : '' }}
                    </li>
                    <li>
                      <i class="fa fa-comment"></i>
                      0 Comments
                    </li>
                  </ul>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="section-title">
          <h3>시청자 <span>투표</span></h3>
        </div>

        {{-- 팀 투표 --}}
        <div class="vote-option set-bg js-team-vote"
          data-setbg="{{ asset('img/news/vote-esports.jpg') }}"
          style="background-size: cover; background-position: top center; background-repeat: no-repeat;">
          <div class="vo-text">
            <h5>올해 Worlds에서 우승할 것 같은 팀은?</h5>

            @foreach ($voteTeams as $team)
              @php
                $teamBg = $team->logo_src ?: asset('img/news/default-team.jpg');
                $teamLabel = $team->short_name ?: $team->name;
              @endphp

              <div class="vt-item" data-player-bg="{{ $teamBg }}">
                <input type="radio" name="worlds-winner" id="worlds-team-{{ $team->id }}" value="{{ $team->id }}">
                <label for="worlds-team-{{ $team->id }}">
                  {{ $teamLabel }}
                </label>
              </div>
            @endforeach
          </div>
        </div>

        {{-- 선수 투표 --}}
        <div class="vote-option set-bg mt-4 js-player-vote"
          data-setbg="{{ asset('img/news/vote-player.jpg') }}"
          style="background-size: cover; background-position: top center; background-repeat: no-repeat;">
          <div class="vo-text">
            <h5>올해 최고의 선수는?</h5>

            @foreach ($votePlayers as $player)
              @php
                $bg = $player->photo_src ?: asset('img/news/default-player.jpg');

                $label = $player->ign ?: ($player->name ?? 'Unknown');
              @endphp

              <div class="vt-item" data-player-bg="{{ $bg }}">
                <input type="radio" name="best-player" id="best-player-{{ $player->id }}" value="{{ $player->id }}">
                <label for="best-player-{{ $player->id }}">
                  {{ $label }}
                </label>
              </div>
            @endforeach
          </div>
        </div>

      </div>
    </div>
  </div>
</section>

<style>
/* 팀 vt-item 정렬 */
.js-team-vote .vt-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 6px 0;
}

/* 팀 로고 아이콘 (라디오 옆) */
.js-team-vote .vt-item::before {
  content: "";
  width: 28px;
  height: 28px;
  background-image: var(--team-logo);
  background-size: contain;
  background-position: center;
  background-repeat: no-repeat;
  border-radius: 4px;
  flex-shrink: 0;
}

/* 선택된 경우 효과 */
.js-team-vote .vt-item.is-selected::before {
  filter: drop-shadow(0 0 4px rgba(255,255,255,0.6));
}

/* --- 팀/선수 공통 이동 애니메이션 --- */
.vote-option.js-player-vote,
.vote-option.js-team-vote {
  position: relative;
  overflow: hidden;
}

.vote-option.js-player-vote .vo-text .vt-item,
.vote-option.js-team-vote .vo-text .vt-item {
  transition: transform 0.6s ease, bottom 0.6s ease, opacity 0.4s ease;
}

/* hide-vote-items 가 붙었을 때 기본 항목 숨기기 */
.vote-option.js-player-vote.hide-vote-items .vo-text .vt-item,
.vote-option.js-team-vote.hide-vote-items .vo-text .vt-item {
  opacity: 0;
  pointer-events: none;
}

/* 그 중 선택된 항목만 맨 아래로 고정 */
.vote-option.js-player-vote.hide-vote-items .vo-text .vt-item.is-selected,
.vote-option.js-team-vote.hide-vote-items .vo-text .vt-item.is-selected {
  opacity: 1;
  pointer-events: auto;
  position: absolute;
  left: 50%;
  bottom: 12px;
  transform: translateX(-50%);
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {

  // 팀 로고를 CSS 변수에 주입 (작은 아이콘용)
  document.querySelectorAll(".js-team-vote .vt-item").forEach(function (item) {
    const bg = item.getAttribute("data-player-bg");
    if (bg) {
      item.style.setProperty("--team-logo", `url("${bg}")`);
    }
  });

  // 투표 공통 로직
  var voteBoxes = document.querySelectorAll('.vote-option');
  if (!voteBoxes.length) return;

  voteBoxes.forEach(function (box) {
    var items = box.querySelectorAll('.vt-item');
    if (!items.length) return;

    items.forEach(function (item) {
      var input = item.querySelector('input[type="radio"]');
      if (!input) return;

      input.addEventListener('change', function () {
        if (!this.checked) return;

        items.forEach(function (i) {
          i.classList.remove('is-selected');
          var s = i.querySelector('.vote-sparkle');
          if (s && s.parentNode) s.parentNode.removeChild(s);
        });

        item.classList.add('is-selected');

        // 스파클 애니메이션
        var sparkle = document.createElement('span');
        sparkle.className = 'vote-sparkle';
        item.appendChild(sparkle);
        sparkle.addEventListener('animationend', function () {
          if (sparkle.parentNode) sparkle.parentNode.removeChild(sparkle);
        });

        var bgUrl = item.getAttribute('data-player-bg');

        // 🔻 팀 박스: 배경 변경 없이 이동 기능만 (dataset.voted 세팅)
        if (box.classList.contains('js-team-vote')) {
          box.dataset.voted = '1';
          return;
        }

        // 🔻 선수 박스만 배경 전환 유지
        if (bgUrl) {
          box.classList.remove('is-bg-visible');
          box.style.setProperty('--vote-bg', 'url("' + bgUrl + '")');
          void box.offsetWidth;
          box.classList.add('is-bg-visible');
          box.dataset.voted = '1';
        }
      });
    });

    box.addEventListener('mouseleave', function () {
      if (box.dataset.voted === '1') box.classList.add('hide-vote-items');
    });

    box.addEventListener('mouseenter', function () {
      if (box.dataset.voted === '1') box.classList.remove('hide-vote-items');
    });
  });
});
</script>
