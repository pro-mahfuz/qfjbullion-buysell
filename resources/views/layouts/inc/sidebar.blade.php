<div class="main-sidebar sidebar-style-2">
  <aside id="sidebar-wrapper">
    <div class="sidebar-brand">
      <a href="#">
        <!-- <img alt="image" src="{{ asset('assets/img/logo.jpg') }}" class="header-logo"> -->
        <span class="logo-name">GOLD TRADING</span>
      </a>
    </div>
    <ul class="sidebar-menu mt-2 mb-2" id="sidebar-menu">

        @can('dashboard')
        <li class="dropdown @yield('dashboard')">
            <a href="{{route('admin.dashboard.buysell')}}" class="nav-link"><i data-feather="monitor"></i><span>Dashboard</span></a>
        </li>
        @endcan
      
        @can('customer')
            <li class="dropdown @yield('dashboard')">
                <a href="{{route('admin.customer.list')}}" class="nav-link"><i data-feather="user-check"></i><span>Customer List</span></a>
            </li>
            @can('customer_add')
            <li class="dropdown @yield('dashboard')">
                <a href="{{route('admin.customer.create')}}" class="nav-link"><i data-feather="user-check"></i><span>Customer Add</span></a>
            </li>
            @endcan
        @endcan

      
      
        <li class="menu-header">Trade</li>
        @can('buysell')
        <li class="dropdown @yield('BuySell')">
            <a href="{{route('admin.buysell.customer.search')}}" class="nav-link"><i data-feather="monitor"></i><span>Buy/Sell</span></a>
        </li>
        @endcan
        @can('Trading')
        <li class="dropdown @yield('RunningTrade')">
            <a href="{{ route('admin.transaction.show.runningOpening', ['type' => '1']) }}" class="nav-link"><i data-feather="monitor"></i><span>Running Trade</span></a>
        </li>
        @endcan
        @can('unmatched_trade')
        <li class="dropdown @yield('UnmatchTrade')">
            <a href="{{route('admin.transaction.show.search')}}" class="nav-link"><i data-feather="monitor"></i><span>Unmatch Trade</span></a>
        </li>
        @endcan
        
        @can('deposit_add')
        <li class="menu-header">Transaction</li>
        <li class="dropdown @yield('Deposit')">
            <a href="{{route('admin.buysell.deposit_withdraw', ['customer_id' => 'null','type' => 'deposit'])}}" class="nav-link"><i data-feather="monitor"></i><span>Deposit</span></a>
        </li>
        
        <li class="dropdown @yield('Withdraw')">
            <a href="{{route('admin.buysell.deposit_withdraw', ['customer_id' => 'null','type' => 'withdraw'])}}" class="nav-link"><i data-feather="monitor"></i><span>Withdraw</span></a>
        </li>
        @endcan
        
        @can('refferal')
            <li class="menu-header">Referral Management</li>
            <li class="dropdown @yield('Referral')">
                <a href="{{route('admin.refferal.list')}}" class="nav-link"><i data-feather="monitor"></i><span>Referral</span></a>
            </li>
        @endcan
        @can('refferal')
            <li class="dropdown @yield('Referral')">
                <a href="{{route('admin.refferal.dashboard')}}" class="nav-link"><i data-feather="monitor"></i><span>Referral Dahsboard</span></a>
            </li>
        @endcan
        
      

        @can('settings')
        <li class="menu-header">Settings</li>
        <li class="dropdown @yield('users')">
            <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="users"></i><span>Users</span></a>
            <ul class="dropdown-menu">
              
              <li class="@yield('users_create')">
                <a class="nav-link" href="{{route('admin.users.create')}}">Add User</a>
              </li>
              
              <li class="@yield('users_list')">
                <a class="nav-link" href="{{url('admin/users')}}">List User</a>
              </li>
              
            </ul>
        </li>
        @endcan
        
        @can('roles')
        <li class="dropdown @yield('roles')">
            <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="check-square"></i><span>Roles</span></a>
            <ul class="dropdown-menu">
              @can('roles_add')
              <li class="@yield('roles_create')">
                <a class="nav-link" href="{{route('admin.role.create')}}">Add Role</a>
              </li>
              @endcan
              <li class="@yield('roles_list')">
                <a class="nav-link" href="{{route('admin.role.list')}}">Role List</a>
              </li>
              
            </ul>
        </li>
        @endcan
        
        
       
        
        @can('system_settings')
        <li class="menu-header">System Settings</li>

        <li class="dropdown @yield('users')">
            <a href="#" class="menu-toggle nav-link has-dropdown"><i data-feather="settings"></i><span>Settings</span></a>
            <ul class="dropdown-menu">
              
              <li class="@yield('users_list')">
                <a class="nav-link" href="{{route('admin.permission.list')}}">Permission</a>
              </li>
              @can('business_settings')
              <li class="@yield('users_create')">
                <a class="nav-link" href="{{route('admin.bussiness.list')}}">Business Settings</a>
              </li>
              
              <li class="@yield('users_list')">
                <a class="nav-link" href="{{route('admin.bussiness.map')}}">Business Map</a>
              </li>
              @endcan
            </ul>
        </li>
        @endcan
       
      <br>
    </ul>
  </aside>
</div>
