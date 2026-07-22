<aside class="main-sidebar {{ config('adminlte.classes_sidebar', 'sidebar-dark-primary elevation-4') }}">

    {{-- Sidebar brand logo --}}
    @if (config('adminlte.logo_img_xl'))
        @include('vendor.client.partials.common.brand-logo-xl')
    @else
        @include('vendor.client.partials.common.brand-logo-xs')
    @endif

    {{-- Sidebar menu --}}
    <div class="sidebar">
        <nav class="pt-2">
            <ul class="nav nav-pills nav-sidebar flex-column {{ config('adminlte.classes_sidebar_nav', '') }}"
                data-widget="treeview" role="menu"
                @if (config('adminlte.sidebar_nav_animation_speed') != 300) data-animation-speed="{{ config('adminlte.sidebar_nav_animation_speed') }}" @endif
                @if (!config('adminlte.sidebar_nav_accordion')) data-accordion="false" @endif>
                {{-- Configured sidebar links --}}

                @php
                    $completed = session()->get('is_completed');
                    $menu =
                        $completed == 1
                            ? [
                                [
                                    'text' => 'Deposits',
                                    'route' => 'client.deposit',
                                    'href' => route('client.deposit.list', ['type' => 'deposit']),
                                    'active' => false,
                                    'class' => '',
                                ],
                                [
                                    'text' => 'Withdraws',
                                    'route' => 'client.deposit',
                                    'href' => route('client.deposit.list', ['type' => 'withdraw']),
                                    'active' => false,
                                    'class' => '',
                                ],
                                [
                                    'text' => 'Transactions',
                                    'route' => 'client.deposit',
                                    'href' => route('client.completed.transactions'),
                                    'active' => false,
                                    'class' => '',
                                ],
                                [
                                    'text' => 'Buy Sell',
                                    'route' => 'client.deposit',
                                    'href' => route('client.buysell'),
                                    'active' => false,
                                    'class' => '',
                                ],
                            ]
                            : [];
                @endphp
                {{-- @dd($menu); --}}
                @each('vendor.client.partials.sidebar.menu-item', $menu, 'item')
            </ul>
        </nav>
    </div>

</aside>
