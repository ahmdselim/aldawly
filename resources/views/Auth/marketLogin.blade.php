@extends('layouts.index')

@section('content')
    <body class="horizontal-layout horizontal-menu blank-page navbar-floating footer-static  " data-open="hover" data-menu="horizontal-menu" data-col="blank-page">
    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row">
            </div>
            <div class="content-body" >
                <div class="auth-wrapper auth-v2">
                    <div class="auth-inner row m-0">

                        <div class="d-none d-lg-flex col-lg-8 align-items-center p-5">
                            <div class="w-100 d-lg-flex align-items-center justify-content-center px-5"><img class="img-fluid" src="{{asset('/app-assets/images/pages/login-v2.svg')}}" alt="Login V2" /></div>
                        </div>
                        <div class="d-flex col-lg-4 align-items-center auth-bg px-2 p-lg-5" >
                            <div class="col-12 col-sm-8 col-md-6 col-lg-12 px-xl-2 mx-auto">
                                <span class="brand-logo">
                         <a class="brand-logo" href="">
                            <img src="{{asset('/app-assets/images/pages/dawlynew.jpg')}}" class="ml-5" style="height: 170px;width: 350px" alt="">
                        </a>
                           </span>
                                <h2 class="card-title font-weight-bold mb-1 mt-1 ">  welcome in eldawly dashboard<span class="wave">👋</span></h2>
                                <p class="card-text mb-2">please enter email and password to login</p>
                                <form class="auth-login-form mt-2" id="log" action="{{route('market.login')}}"  method="POST">
                                    @csrf
                                    @if(session()->has('err'))
                                        <div class="alert alert-warning alert-danger fade show" role="alert">
                                            <strong>Error :</strong> {{session()->get('err')}}
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    @endif
                                    <div class="form-group" id="Email_validation">
                                        <label class="" style=" direction: rtl!important" for="login-email">email</label>
                                        <input class="form-control email" id="login-email" onkeypress="makelogin(event)" type="text" name="email" placeholder="john@example.com" aria-describedby="login-email" autofocus="" tabindex="1" required />
                                    </div>
                                    <div class="form-group">
                                        <label style="direction: rtl !important;" class="form-label" for="login-password">password</label>
                                        <div class="input-group input-group-merge form-password-toggle" id="password_validation">
                                            <input class="form-control form-control-merge password" id="login-password" type="password" name="password" placeholder="············" aria-describedby="login-password" tabindex="2" required />
                                            <div class="input-group-append"><span class="input-group-text cursor-pointer"><i data-feather="eye"></i></span></div>
                                        </div>
                                    </div>
                                    <button class="btn btn-primary btn-block" id="signin" tabindex="4" style="background-color: blue!important;">Login</button>
                                </form>
                            </div>
                        </div>
                        <!-- /Login-->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $("#signin").click(function(event){
            event.preventDefault();
            if($(".email").val()==""||$(".password").val()==""){
                if($(".email").val()==""){
                    x=`<div class="valid-tooltip" style="display: block; z-index:1;    position: inherit;"> email required</div>`;
                    $("#Email_validation").append(x);
                }if($(".password").val()==""){
                    x=`<div class="valid-tooltip"  style="display: block; z-index:1;    position: inherit;">password required</div>`;
                    $("#password_validation").append(x);
                }
                setTimeout((function() {
                    $(".valid-tooltip").remove();
                }), 1000);
            }else{
                $('#log').submit();
            }
        });
        function makelogin(event){
            if(event["key"]=="Enter"){
                $("#signin").click()
            }
        }
    </script>

@endsection




