<div class="profile-sidebar sidenav sidenav-fixed">
    <!-- SIDEBAR USERPIC -->
    <div class="profile-userpic">
        <img src="{{asset('/test.jpg')}}" class="img-responsive" alt="">
    </div>
    <!-- END SIDEBAR USERPIC -->
    <!-- SIDEBAR USER TITLE -->
    <div class="profile-usertitle">
        <div class="profile-usertitle-name">
            Marcus Doe
        </div>
        <div class="profile-usertitle-job">
            Developer
        </div>
    </div>
    <!-- END SIDEBAR USER TITLE -->
    <!-- SIDEBAR BUTTONS -->
    <div class="profile-userbuttons">
        <button type="button" class="btn btn-success btn-sm">Follow</button>
        <button type="button" class="btn btn-danger btn-sm">Message</button>
    </div>
    <!-- END SIDEBAR BUTTONS -->
    <!-- SIDEBAR MENU -->
    <div class="profile-usermenu">
        <ul class="nav navbar-nav">
            <li class="bold @if(request()->route()->getName() == 'homepage')active @endif ">
                <a href="{{route('homepage')}}" class="waves-effect waves-teal">
                    <span class="oi oi-circle-check" aria-hidden="true"></span>
                    Homepage </a>
            </li>
            <li class="bold @if(request()->route()->getName() == 'profile_cv')active @endif">
                <a href="{{route("profile_cv")}}">
                    <span class="oi oi-rain" aria-hidden="true"></span>
                    CV </a>
            </li>
            <li class="bold @if(request()->route()->getName() == 'profile_job')active @endif">
                <a href="{{route("profile_job")}}" target="_blank">
                    <span class="oi oi-envelope-open" aria-hidden="true"></span>
                    Jobs </a>
            </li>
            <li class="bold @if(request()->route()->getName() == 'recommendToday')active @endif">
                <a href="{{route("recommendToday")}}" target="_blank">
                    <span class="oi oi-envelope-open" aria-hidden="true"></span>
                    Recommend Today </a>
            </li>

        </ul>
    </div>
    <!-- END MENU -->
</div>