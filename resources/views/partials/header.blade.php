<header class="header-section">
  <div class="header__nav">
    <div class="container">
      <div class="row align-items-center">
        {{-- 로고 영역 --}}
        <div class="col-lg-2 col-6">
          <div class="logo">
            <a href="{{ route('home') }}"><img src="{{ asset('img/logo.png') }}" alt=""></a>
          </div>
        </div>

        {{-- 메뉴 + 시간/로그인 영역 --}}
        <div class="col-lg-10 col-6">
          <div class="nav-menu">

            {{-- 메인 메뉴 + 오른쪽 시간/로그인 --}}
            <ul class="main-menu">
              @auth
                @if(auth()->user()->is_admin)
                  <li class="nav-admin {{ request()->routeIs('admin.*') ? 'active' : '' }}">
                    <a href="{{ route('admin.dashboard') }}">관리자</a>
                  </li>
                @endif
              @endauth
              <li class="{{ request()->routeIs('home') ? 'active' : '' }}">
                <a href="{{ route('home') }}">홈</a>
              </li>
              <li class="{{ request()->routeIs('players.*') ? 'active' : '' }}">
                <a href="{{ route('players.index') }}">선수</a>
              </li>
              <li class="{{ request()->routeIs('teams.*') ? 'active' : '' }}">
                <a href="{{ route('teams.index') }}">팀</a>
              </li>
              <li class="{{ request()->routeIs('matches.*') ? 'active' : '' }}">
                <a href="{{ route('matches.index') }}">경기</a>
              </li>
              <li class="{{ request()->routeIs('rankings.*') ? 'active' : '' }}">
                <a href="{{ route('rankings.index') }}">순위</a>
              </li>
              <li class="{{ request()->routeIs('news.*') ? 'active' : '' }}">
                <a href="{{ route('news.index') }}">뉴스</a>
              </li>

              {{-- ▼ 여기부터 오른쪽 정렬 영역 --}}
              <li class="menu-spacer"></li>

              {{-- 시간 --}}
              <li class="menu-clock">
                <i class="fa fa-clock-o menu-clock-icon"></i>
                <span id="live-clock" class="menu-clock-text" data-tz="{{ config('app.timezone', 'Asia/Seoul') }}">
                  00:00:00 · Nov 28 2025
                </span>
              </li>


              {{-- 회원가입 / 로그인 / 마이페이지 / 로그아웃 --}}
              @guest
                <li class="menu-auth"><a href="{{ route('register') }}">회원가입</a></li>
                <li class="menu-auth"><a href="{{ route('login') }}">로그인</a></li>
              @endguest

              @auth
                <li class="menu-auth">
                  <a href="{{ route('account.show') }}">
                    {{ auth()->user()->name }}님
                  </a>
                </li>
                <li class="menu-auth">
                  <form method="post" action="{{ route('logout') }}" class="menu-auth-logout-form">
                    @csrf
                    <button type="submit" class="menu-auth-logout-btn">
                      로그아웃
                    </button>
                  </form>
                </li>
              @endauth
            </ul>

          </div>
        </div>
      </div>
    </div>
  </div>
</header>

<script>
  (function () {
    const el = document.getElementById('live-clock');
    if (!el) return;

    const tz = el.dataset.tz || 'Asia/Seoul';

    // 시간: 13:40:46
    const timeFmt = new Intl.DateTimeFormat('en-US', {
      timeZone: tz,
      hour: '2-digit',
      minute: '2-digit',
      second: '2-digit',
      hour12: false,
    });

    // 날짜: Nov 28 2025 (콤마 빼서 한 줄 느낌 살리기)
    const dateFmt = new Intl.DateTimeFormat('en-US', {
      timeZone: tz,
      month: 'short',
      day: '2-digit',
      year: 'numeric',
    });

    function tick() {
      const now = new Date();
      const time = timeFmt.format(now);        // 13:40:46
      let date = dateFmt.format(now);          // Nov 28, 2025
      date = date.replace(',', '');            // Nov 28 2025

      el.innerHTML = `
        <span class="clock-time">${time}</span>
        <span class="clock-dot">·</span>
        <span class="clock-date">${date}</span>
      `;
    }

    tick();
    setInterval(tick, 1000);
  })();
</script>