<div class="offcanvas-menu-overlay"></div>
<div class="offcanvas-menu-wrapper">
  <div class="canvas-close"><i class="fa fa-close"></i></div>
  <div class="search-btn search-switch"><i class="fa fa-search"></i></div>

  <div class="header__top--canvas">
    <div class="ht-info">
      <ul>
        <li>20:00 - May 19, 2019</li>
        <li><a href="#">Sign in</a></li>
        <li><a href="#">Contact</a></li>
      </ul>
    </div>
    <div class="ht-links">
      <a href="#"><i class="fa fa-facebook"></i></a>
      <a href="#"><i class="fa fa-vimeo"></i></a>
      <a href="#"><i class="fa fa-twitter"></i></a>
      <a href="#"><i class="fa fa-google-plus"></i></a>
      <a href="#"><i class="fa fa-instagram"></i></a>
    </div>
  </div>

  <ul class="main-menu mobile-menu">
    <li class="{{ request()->routeIs('home') ? 'active' : '' }}"><a href="{{ route('home') }}">Home</a></li>
    <li class="{{ request()->routeIs('players.*') ? 'active' : '' }}"><a href="{{ route('players.index') }}">Players</a></li>
    <li><a href="#">Schedule</a></li>
    <li><a href="#">Results</a></li>
    <li><a href="#">Sport</a></li>
    <li><a href="#">Pages</a>
      <ul class="dropdown">
        <li><a href="#">Blog</a></li>
        <li><a href="#">Blog Details</a></li>
        <li><a href="#">Schedule</a></li>
        <li><a href="#">Results</a></li>
      </ul>
    </li>
    <li><a href="#">Contact Us</a></li>
  </ul>
  <div id="mobile-menu-wrap"></div>
</div>
