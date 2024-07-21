{{--<!-- BEGIN: Header-->--}}
{{--<nav class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-light navbar-shadow container-xxl">--}}
{{--    <div class="navbar-container d-flex content">--}}
{{--        <div class="bookmark-wrapper d-flex align-items-center">--}}
{{--            <ul class="nav navbar-nav d-xl-none">--}}
{{--                <li class="nav-item"><a class="nav-link menu-toggle" href="javascript:void(0);"><i class="ficon" data-feather="menu"></i></a></li>--}}
{{--            </ul>--}}


{{--        </div>--}}
{{--        <ul class="nav navbar-nav align-items-center ml-auto">--}}
{{--            <li class="nav-item dropdown dropdown-user"><a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">--}}
{{--                                <div class="user-nav d-sm-flex d-none"><span class="user-name font-weight-bolder">{{Auth::guard('web')->user()->name}}</span><span class="user-status">{{auth()->user()->user_type}}</span></div><span class="avatar"><img class="round" src="https://cloudy-eg.com/public/listme/public/app-assets/images/portrait/small/avatar-s-11.jpg" alt="avatar" height="40" width="40"><span class="avatar-status-online"></span></span>--}}
{{--                            </a>--}}
{{--                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-user">--}}

{{--                                <div class="dropdown-divider">--}}

{{--                                </div>--}}
{{--                                <a class="dropdown-item" href="{{url('logout')}}"><i class="mr-50" data-feather="power"></i> Logout</a>--}}
{{--                            </div>--}}
{{--                        </li>--}}
{{--        </ul>--}}
{{--    </div>--}}
{{--</nav>--}}

<nav class="header-navbar navbar navbar-expand-lg align-items-center floating-nav navbar-light navbar-shadow container-xxl">
    <div class="navbar-container d-flex content">
        <ul class="nav navbar-nav d-xl-none">
            <li class="nav-item"><a class="nav-link menu-toggle" href="javascript:void(0);"><i class="ficon" data-feather="menu"></i></a></li>
        </ul>
        <ul class="nav navbar-nav align-items-center ml-auto">
            <li class="nav-item d-none d-lg-block"><a class="nav-link nav-link-style"><i class="ficon" data-feather="moon"></i></a></li>

            <li class="nav-item dropdown dropdown-user"><a class="nav-link dropdown-toggle dropdown-user-link" id="dropdown-user" href="javascript:void(0);" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="user-nav d-sm-flex d-none"><span class="user-name font-weight-bolder">{{Auth::guard('web')->user()->name}}</span><span class="user-status">{{auth()->user()->user_type}}</span></div><span class="avatar"><img class="round" src="{{asset('app-assets/images/portrait/small/avatar-s-11.jpg')}}" alt="avatar" height="40" width="40"><span class="avatar-status-online"></span></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown-user">

                    <div class="dropdown-divider">

                    </div>
                    <a class="dropdown-item" href="{{url('logout')}}"><i class="mr-50" data-feather="power"></i> Logout</a>
                </div>
            </li>
        </ul>
    </div>
</nav>
