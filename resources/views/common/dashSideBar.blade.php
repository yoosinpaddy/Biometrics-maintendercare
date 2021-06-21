
  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{route('default')}}" class="brand-link">
        <img style="background: #ffffff;border: 2px solid;border-radius: 50px 20px;margin-top: 5px;max-width: 80px;" src="{{route('default')}}/images/logo.png" width="236" height="40" alt="My writer Logo">

      <span class="brand-text font-weight-light">Tender Care</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          {{-- <img src="{{route('default').$user->profile_image}}" class="img-circle elevation-2" alt="@if (Auth::user()){{Auth::user()->name}}@endif"> --}}
          <img src="{{route('default')}}/images/logo.png" class="img-circle elevation-2" alt="@if (Auth::user()){{Auth::user()->name}}@endif">
        </div>
        <div class="info">
          <a href="#" class="d-block">@if (Auth::user())
            {{Auth::user()->name}}
          @endif</a>
        </div>
      </div>

      <!-- SidebarSearch Form -->
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->

          <li class="nav-header">Students</li>
          <li class="nav-item">
            <a href="{{route('school.home')}}" class="nav-link @if(Route::is('school.home'))active @endif">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Enrolment
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{route('school.parents')}}" class="nav-link  @if(Route::is('school.parents'))active @endif">
                <i class="nav-icon fas fa-user-alt"></i>
              <p>
                Parents
              </p>
            </a>
          </li>
                  @php
                      $myDate=date("Y-m-d");
                  @endphp
              <li class="nav-item">
                <a href="{{route('school.detailedReports',['class'=>'all','stream'=>'all','day'=>$myDate])}}" class="nav-link @if(Route::is('school.detailedReports'))active @endif">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Reports</p>
                </a>
              </li>

              <li class="nav-header">Staff Reports</li>
              <li class="nav-item">
                <a href="{{route('staff.reports',['type'=>'teaching','day'=>$myDate])}}" class="nav-link">
                  <i class="nav-icon fas fa-user-alt"></i>
                  <p>
                    Teaching
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="{{route('staff.reports',['type'=>'non-teaching','day'=>$myDate])}}" class="nav-link  ">
                    <i class="nav-icon fas fa-user-alt"></i>
                  <p>
                    Non-Teaching
                  </p>
                </a>
              </li>

          <li class="nav-header">SMS</li>


          <li class="nav-item">
            <a href="{{route('sms.templete')}}" class="nav-link @if(Route::is('school.send.sms'))active @endif">
                <i class="fas fa-book"></i>              <p>
                Templetes
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{route('school.bulkSms',['class'=>'all','stream'=>'all'])}}" class="nav-link @if(Route::is('school.reports.sms'))active @endif">
                <i class="fas fa-inbox"></i>              <p>
                Send Bulk
              </p>
            </a>
          </li>
            @if (\Illuminate\Support\Facades\Auth::user()->id==1)
                <li class="nav-item">
                    <a href="{{route('delivery.reports.sms')}}" class="nav-link @if(Route::is('delivery.reports.sms'))active @endif">
                        <i class="fas fa-inbox"></i>              <p>
                            Premium Delivery reports
                        </p>
                    </a>
                </li>

            @endif

          <li class="nav-header">OTHER</li>

          <li class="nav-item">
            <a href="{{route('school.streams')}}" class="nav-link @if(Route::is('school.streams'))active @endif">
                <i class="fas fa-code-branch"></i>             <p>
                Streams Setup
              </p>
            </a>
          </li>
          <li class="nav-item">
            <a href="{{route('school.logout')}}" class="nav-link @if(Route::is('school.logout'))active @endif">
                <i class="fas fa-sign-out-alt"></i>            <p>
                Logout
              </p>
            </a>
          </li>


        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
