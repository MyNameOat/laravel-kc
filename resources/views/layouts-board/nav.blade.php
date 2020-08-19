<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element"> <span>
                            <img alt="image" class="img-circle" src="/img/profile_small.jpg" />
                             </span>
                    <a href="#">
                            <span class="clear"> <span class="block m-t-xs"> <strong class="font-bold">{{ auth()->user()->name }}</strong>
                             </span> <span class="text-muted text-xs block">KC Employee </span> </span> </a>
                </div>
                <div class="logo-element">
                    KC+
                </div>
            </li>
            {{-- <li>
                <a href="{{ route('select-program') }}"><i class="fa fa-diamond"></i> <span class="nav-label">กลับหน้าเลือกโปรแกรม</span></a>
            </li>
            <li class="{{ route::is('board.dashboard') ? 'active' : '' }}">
                <a href="{{ route('board.dashboard') }}"><i class="fa fa-diamond"></i> <span class="nav-label">กระดานสรุป</span></a>
            </li>
            <li class="{{ route::is('board.summary-sale.*') ? 'active' : '' }}">
                <a href="#"><i class="fa fa-diamond"></i> <span class="nav-label">กระดานสรุปยอดขาย</span> <span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li class="{{ route::is('board.summary-sale.current-day') ? 'active' : '' }}">
                        <a href="{{ route('board.summary-sale.current-day') }}">วันปัจจุบัน</a>
                    </li>
                </ul>
            </li> --}}
        </ul>

    </div>
</nav>
