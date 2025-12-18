<aside class="left-sidebar">
    <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
            <a href="{{ route('admin.dashboard') }}" class="text-nowrap logo-img">
                <img src="{{ asset('assets/admin/images/logos/logo.svg') }}" alt="" />
            </a>
            <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
                <i class="ti ti-x fs-6"></i>
            </div>
        </div>
        <nav class="sidebar-nav scroll-sidebar mt-4" data-simplebar="">
            <ul id="sidebarnav">

                <li class="sidebar-item">
                    <a class="sidebar-link {{ Request::segment(2) == 'dashboard' ? 'active' : '' }}"
                        href="{{ route('admin.dashboard') }}" aria-expanded="false">
                        <i class="ti ti-atom"></i>
                        <span class="hide-menu">Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ in_array(Request::segment(2), ['user', 'add_user', 'edit_user']) ? 'active' : '' }}"
                        href="{{ route('admin.user') }}" aria-expanded="false">
                        <i class="ti ti-user-circle"></i>
                        <span class="hide-menu">Users</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ in_array(Request::segment(2), ['plans', 'plans_create', 'plans_edit']) ? 'active' : '' }}"
                        href="{{ route('admin.plan_index') }}" aria-expanded="false">
                        <i class="ti ti-user-circle"></i>
                        <span class="hide-menu">Plans</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ in_array(Request::segment(2), ['referral_setting']) ? 'active' : '' }}"
                        href=" {{ route('admin.referral_setting') }}" aria-expanded="false">

                        <i class="ti ti-user-circle"></i>
                        <span class="hide-menu">Referral Setting</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ in_array(Request::segment(2), ['user_plans']) ? 'active' : '' }}"
                        href="{{ route('admin.user_plan') }}" aria-expanded="false">
                        <i class="ti ti-user-circle"></i>
                        <span class="hide-menu">User Plans</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ in_array(Request::segment(2), ['transactions']) ? 'active' : '' }}"
                        href="{{ route('admin.transaction') }}" aria-expanded="false">
                        <i class="ti ti-user-circle"></i>
                        <span class="hide-menu">Transactions</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link justify-content-between has-arrow {{ in_array(Request::segment(2), ['term_conditions', 'privacy_policy', 'edit_user']) ? 'active' : '' }}"
                        href="javascript:void(0)" aria-expanded="false">
                        <div class="d-flex align-items-center gap-3">
                            <span class="d-flex">
                                <i class="ti ti-cards"></i>
                            </span>
                            <span class="hide-menu">Pages Management</span>
                        </div>
                    </a>
                    <ul aria-expanded="false" class="collapse first-level">
                        <li class="sidebar-item">
                            <a class="sidebar-link justify-content-between {{ in_array(Request::segment(2), ['term_conditions']) ? 'active' : '' }}"
                                href="{{ route('admin.term_conditions') }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Term & Condition</span>
                                </div>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link justify-content-between {{ in_array(Request::segment(2), ['privacy_policy']) ? 'active' : '' }}"
                                href="{{ route('admin.privacy_policy') }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Privacy & Policy</span>
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>

        </nav>
    </div>
</aside>
