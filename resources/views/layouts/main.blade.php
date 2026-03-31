<!DOCTYPE html>
<html lang="zxx">
  @if(session('admin_alert'))
  <script>
    alert(@json(session('admin_alert')));
  </script>
@endif

  @include('partials.head')

  <body class="@yield('body_class')">
    {{-- Page Preloader --}}
    <div id="preloder"><div class="loader"></div></div>

    {{-- Offcanvas --}}
    @include('partials.offcanvas')

    {{-- Header --}}
    @include('partials.header')

    {{-- Page Content --}}
    <main>@yield('content')</main>

    {{-- Footer --}}
    @include('partials.footer')

    {{-- JS --}}
    @include('partials.scripts')
    @stack('scripts')
    <div class="scroll-tools">
      <button type="button" class="scroll-btn scroll-up">
        <i class="fa fa-angle-up"></i>
      </button>
      <button type="button" class="scroll-btn scroll-down">
        <i class="fa fa-angle-down"></i>
      </button>
    </div>

  </body>
</html>
<script>
(function() {
  const tools = document.querySelector('.scroll-tools');
  if (!tools) return;

  const up = tools.querySelector('.scroll-up');
  const down = tools.querySelector('.scroll-down');

  function smoothScrollTo(targetY, duration) {
    const startY = window.scrollY || window.pageYOffset;
    const distance = targetY - startY;
    const startTime = performance.now();
    const d = duration || 500;

    function step(now) {
      const elapsed = now - startTime;
      const t = Math.min(1, elapsed / d);
      const eased = 0.5 * (1 - Math.cos(Math.PI * t));
      window.scrollTo(0, startY + distance * eased);
      if (t < 1) requestAnimationFrame(step);
    }

    requestAnimationFrame(step);
  }

  function toggleVisibility() {
    if (window.scrollY > 200) {
      tools.classList.add('visible');
    } else {
      tools.classList.remove('visible');
    }
  }

  up.addEventListener('click', function() {
    smoothScrollTo(0, 600);
  });

  down.addEventListener('click', function() {
    const h = Math.max(
      document.body.scrollHeight,
      document.documentElement.scrollHeight
    );
    smoothScrollTo(h, 600);
  });

  window.addEventListener('scroll', toggleVisibility);
  toggleVisibility();
})();
</script>
