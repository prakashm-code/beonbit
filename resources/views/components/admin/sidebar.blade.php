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
                {{-- <li class="sidebar-item">
                    <a class="sidebar-link justify-content-between has-arrow" href="javascript:void(0)"
                        aria-expanded="false">
                        <div class="d-flex align-items-center gap-3">
                            <span class="d-flex">
                                <i class="ti ti-layout-grid"></i>
                            </span>
                            <span class="hide-menu">Front Pages</span>
                        </div>
                    </a>
                    <ul aria-expanded="false" class="collapse first-level">
                        <li class="sidebar-item">
                            <a class="sidebar-link justify-content-between" target="_blank"
                                href="https://bootstrapdemos.adminmart.com/modernize/dist/main/frontend-landingpage.html">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Homepage</span>
                                </div>
                                <span class="hide-menu badge bg-secondary-subtle text-secondary fs-1 py-1">Pro</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link justify-content-between" target="_blank"
                                href="https://bootstrapdemos.adminmart.com/modernize/dist/main/frontend-aboutpage.html">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">About Us</span>
                                </div>
                                <span class="hide-menu badge bg-secondary-subtle text-secondary fs-1 py-1">Pro</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link justify-content-between" target="_blank"
                                href="https://bootstrapdemos.adminmart.com/modernize/dist/main/frontend-blogpage.html">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Blog</span>
                                </div>
                                <span class="hide-menu badge bg-secondary-subtle text-secondary fs-1 py-1">Pro</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link justify-content-between" target="_blank"
                                href="https://bootstrapdemos.adminmart.com/modernize/dist/main/frontend-blogdetailpage.html">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Blog Details</span>
                                </div>
                                <span class="hide-menu badge bg-secondary-subtle text-secondary fs-1 py-1">Pro</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link justify-content-between" target="_blank"
                                href="https://bootstrapdemos.adminmart.com/modernize/dist/main/frontend-contactpage.html">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Contact Us</span>
                                </div>
                                <span class="hide-menu badge bg-secondary-subtle text-secondary fs-1 py-1">Pro</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link justify-content-between" target="_blank"
                                href="https://bootstrapdemos.adminmart.com/modernize/dist/main/frontend-portfoliopage.html">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Portfolio</span>
                                </div>
                                <span class="hide-menu badge bg-secondary-subtle text-secondary fs-1 py-1">Pro</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link justify-content-between" target="_blank"
                                href="https://bootstrapdemos.adminmart.com/modernize/dist/main/frontend-pricingpage.html">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="round-16 d-flex align-items-center justify-content-center">
                                        <i class="ti ti-circle"></i>
                                    </div>
                                    <span class="hide-menu">Pricing</span>
                                </div>
                                <span class="hide-menu badge bg-secondary-subtle text-secondary fs-1 py-1">Pro</span>
                            </a>
                        </li>
                    </ul>
                </li> --}}

                {{-- <li>
                    <span class="sidebar-divider lg"></span>
                </li> --}}

            </ul>

        </nav>
    </div>
</aside>
