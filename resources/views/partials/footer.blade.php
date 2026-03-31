<footer class="footer-section set-bg" data-setbg="{{ asset('img/footer-bg.png') }}">
  <div class="container">
    <div class="row">
      <div class="col-lg-3 col-md-6">
        <div class="fs-logo">
          <div class="logo">
            <a href="{{ route('home') }}"><img src="{{ asset('img/logo.png') }}" alt=""></a>
          </div>
          <ul>
            <li><i class="fa fa-envelope"></i> 202212004@induk.ac.kr</li>
            <li><i class="fa fa-copy"></i> +(12) 345 6789</li>
            <li><i class="fa fa-thumb-tack"></i> 라라벨 개인 프로젝트</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="copyright-option">
      <div class="row align-items-center">
        <div class="col-lg-7 col-md-7">
          <div class="co-text">
            <p>
              Copyright &copy; {{ date('Y') }}
              All rights reserved | This site is made by Hwangminseo
            </p>
          </div>
        </div>
        <div class="col-lg-5 col-md-5">
          <div class="co-widget">
            <ul>
              <li><a href="{{ route('legal.copyright') }}">Copyright notification</a></li>
              <li><a href="{{ route('legal.terms') }}">Terms of Use</a></li>
              <li><a href="{{ route('legal.privacy') }}">Privacy Policy</a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</footer>

<style>
  .footer-section.set-bg {
    position: relative;
    overflow: hidden;
    background: #120307 url("{{ asset('img/footer-bg.png') }}") center/cover no-repeat;
    padding: 48px 0 24px;
    color: #fbeff0;
  }

  .footer-section.set-bg::before {
    content: "";
    position: absolute;
    inset: 0;
    background:
      radial-gradient(circle at top, rgba(255, 90, 110, 0.5), transparent 55%),
      linear-gradient(to bottom, rgba(12, 2, 4, 0.96), rgba(6, 1, 3, 0.98));
    mix-blend-mode: multiply;
    z-index: 0;
  }

  .footer-section.set-bg>.container,
  .footer-section.set-bg .copyright-option {
    position: relative;
    z-index: 1;
  }

  .fs-logo .logo img {
    max-width: 150px;
    height: auto;
    display: block;
    margin-bottom: 16px;
    filter: drop-shadow(0 0 10px rgba(0, 0, 0, 0.8));
  }

  .fs-logo ul {
    list-style: none;
    margin: 0 0 18px;
    padding: 0;
    font-size: 0.9rem;
    color: rgba(251, 239, 240, 0.85);
  }

  .fs-logo ul li {
    display: flex;
    align-items: center;
    margin-bottom: 6px;
  }

  .fs-logo ul li i {
    margin-right: 8px;
    font-size: 0.95rem;
    color: #ff6b6b;
  }

  .fs-social {
    display: flex;
    gap: 8px;
  }

  .fs-social a {
    width: 32px;
    height: 32px;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.06);
    color: #fbeff0;
    font-size: 0.9rem;
    text-decoration: none;
    transition: all 0.18s ease;
  }

  .fs-social a:hover {
    background: #ff3b4d;
    color: #ffffff;
    transform: translateY(-1px);
  }

  .copyright-option {
    margin-top: 28px;
    padding-top: 14px;
    border-top: 1px solid rgba(255, 170, 170, 0.35);
  }

  .co-text p {
    margin: 0;
    font-size: 0.85rem;
    color: rgba(251, 239, 240, 0.78);
  }

  .co-widget ul {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    justify-content: flex-end;
    gap: 16px;
    font-size: 0.8rem;
  }

  .co-widget ul li a {
    color: rgba(251, 239, 240, 0.7);
    text-decoration: none;
    transition: color 0.15s ease;
  }

  .co-widget ul li a:hover {
    color: #ffd4da;
  }

  @media (max-width: 767.98px) {
    .footer-section.set-bg {
      padding: 36px 0 20px;
    }

    .co-widget ul {
      justify-content: center;
      margin-top: 8px;
      flex-wrap: wrap;
    }

    .co-text {
      text-align: center;
      margin-bottom: 6px;
    }
  }
</style>