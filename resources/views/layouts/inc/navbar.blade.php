<nav class="navbar navbar-expand-lg main-navbar sticky">
  <div class="mr-3" id="navbar_search_box">
    <ul class="navbar-nav">
      <li>
        <a href="#" data-toggle="sidebar" class="nav-link nav-link-lg collapse-btn"> <i data-feather="align-justify"></i></a>
      </li>
      <li>
        <a href="#" class="nav-link nav-link-lg fullscreen-btn"><i data-feather="maximize"></i></a>
      </li>
      <li>
        <div class="search-element d-none d-md-block">
          <form class="form-inline" action="#" method="POST">
            <input class="form-control rounded-pill pr-5" type="search" name="search" id="search" placeholder="Search" aria-label="Search" data-width="300" required="">
            <button class="btn text-dark border-0 rounded-pill ml-n5" type="submit">
              <i class="fa fa-search"></i>
            </button>
            <div id="navbar_search_result" class="navbar-search-box" style="display:none"></div>
          </form>
        </div>
      </li>
    </ul>
  </div>
  <ul class="navbar-nav navbar-left mr-auto">
  </ul>
  
  <ul class="navbar-nav navbar-right">
    <li class="dropdown">
      <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
        <!--@if (Auth::user()->photo)-->
        <!--  <img alt="image" src="{{ asset('uploads/users/'.Auth::user()->photo) }}" class="user-img-radious-style">-->
        <!--@else-->
          <img alt="image" src="{{ asset('uploads/avator.png') }}" class="user-img-radious-style">
        <!--@endif-->
        <span class="d-sm-none d-lg-inline-block"></span>
      </a>
      <div class="dropdown-menu dropdown-menu-right pullDown">
        <div class="dropdown-title">{{ Auth::user()->name }}</div>
        <a href="#" class="dropdown-item has-icon"> <i class="far fa-user"></i> Profile</a>
        
        <div class="dropdown-divider"></div>
        <a href="#" class="dropdown-item has-icon text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
          <i class="fa fa-power-off text-danger"></i> Logout
        </a>
        <form id="logout-form" action="{{route('admin.logout')}}" method="POST" style="display: none;">
          @csrf
        </form>
      </div>
    </li>
  </ul>
</nav>