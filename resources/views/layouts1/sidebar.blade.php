<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a class="app-brand-link">

            <span class="app-brand-text demo menu-text fw-bold"><img src="/assets/img/App logo.png" alt=""></span>
        </a>

        {{-- <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
        </a> --}}
    </div>
    <div class="brandborder">

    </div>

    {{-- <div class="menu-inner-shadow"></div> --}}




    <ul class="menu-inner py-1">
        <!-- Dashboards -->




        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Dashboard</span>
        </li>
        <li class="menu-item {{ Request::url() == route('dashboard-') ? 'active' : '' }}">
            <a href="{{ route('dashboard-') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Statistics">Statistics</div>
            </a>
        </li>


        <!-- Apps & Pages -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">User Managements</span>
        </li>
        <li class="menu-item {{ Request::url() == route('dashboard-users', 'individual') ? 'active' : '' }}">
            <a href="{{ route('dashboard-users', 'individual') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="User">Users</div>
            </a>
        </li>

        <li class="menu-item {{ Request::url() == route('dashboard-verify-users') ? 'active' : '' }}">
            <a href="{{ route('dashboard-verify-users') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="User">Verifications Requests</div>

            </a>
        </li>
        <li class="menu-item {{ Request::url() == route('dashboard-report-', 'individual') ? 'active' : '' }}">
            <a href="{{ route('dashboard-report-', 'individual') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="User">Reported Accounts</div>

            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Facility Managements</span>
        </li>

        <li class="menu-item {{ Request::url() == route('dashboard-users', 'facility') ? 'active' : '' }}">
            <a href="{{ route('dashboard-users', 'facility') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="User">Accounts</div>
            </a>
        </li>
        <li class="menu-item {{ Request::url() == route('dashboard-report-', 'facility') ? 'active' : '' }}">
            <a href="{{ route('dashboard-report-', 'facility') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="User">Reported Accounts</div>

            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Organizations Managements </span>
        </li>
        <li class="menu-item {{ Request::url() == route('dashboard-users', 'organization') ? 'active' : '' }}">
            <a href="{{ route('dashboard-users', 'organization') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="User">Accounts</div>
            </a>
        </li>
        <li class="menu-item {{ Request::url() == route('dashboard-user-organization-verify') ? 'active' : '' }}">
            <a href="{{ route('dashboard-user-organization-verify') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="User">Verifications Requests</div>

            </a>
        </li>
        <li class="menu-item {{ Request::url() == route('dashboard-report-', 'organization') ? 'active' : '' }}">
            <a href="{{ route('dashboard-report-', 'organization') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="User">Reported Accounts</div>

            </a>
        </li>


        <li class="menu-header small text-uppercase">
            <span class="menu-header-text"> Reported Requests</span>
        </li>
        <li class="menu-item {{ Request::url() == route('dashboard-category-', 'report') ? 'active' : '' }}">
            <a href="{{ route('dashboard-category-', 'report') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Report Categories</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Interests</span>
        </li>
        <li class="menu-item {{ Request::url() == route('dashboard-category-', 'interest') ? 'active' : '' }}">
            <a href="{{ route('dashboard-category-', 'interest') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Interest Categories</div>
            </a>
        </li>



        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Forum</span>
        </li>
        <li class="menu-item {{ Request::url() == route('dashboard-category-', 'forum') ? 'active' : '' }}">
            <a href="{{ route('dashboard-category-', 'forum') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Forum Categories</div>
            </a>
        </li>
        <li class="menu-item {{ Request::url() == route('dashboard-report-', 'forum') ? 'active' : '' }}">
            <a href="{{ route('dashboard-report-', 'forum') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="User">Reported Forum</div>

            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">News & Articles </span>
        </li>
        <li class="menu-item {{ Request::url() == route('dashboard-category-', 'news') ? 'active' : '' }}">
            <a href="{{ route('dashboard-category-', 'news') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">News Categories</div>
            </a>
        </li>
        <li class="menu-item {{ Request::url() == route('dashboard-report-', 'news') ? 'active' : '' }}">
            <a href="{{ route('dashboard-report-', 'news') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="User">Reported News</div>

            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Events </span>
        </li>
        <li class="menu-item {{ Request::url() == route('dashboard-category-', 'event') ? 'active' : '' }}">
            <a href="{{ route('dashboard-category-', 'event') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Events Categories</div>
            </a>
        </li>
        <li class="menu-item {{ Request::url() == route('dashboard-report-', 'event') ? 'active' : '' }}">
            <a href="{{ route('dashboard-report-', 'event') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="User">Reported Event</div>

            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Help & Supports</span>
        </li>
        <li class="menu-item {{ Request::url() == route('dashboard-category-', 'position') ? 'active' : '' }}">
            <a href="{{ route('dashboard-category-', 'position') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Position Categories</div>
            </a>
        </li>
        <li class="menu-item {{ Request::url() == route('dashboard-ticket-ticket', 'active') ? 'active' : '' }}">
            <a href="{{ route('dashboard-ticket-ticket', 'active') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div> Active Tickets </div>

            </a>
        </li>
        <li class="menu-item {{ Request::url() == route('dashboard-ticket-ticket', 'close') ? 'active' : '' }} ">
            <a href="{{ route('dashboard-ticket-ticket', 'close') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>

                <div>Closed Tickets</div>
            </a>
        </li>
        <li class="menu-item {{ Request::url() == route('dashboard-category-', 'ticket') ? 'active' : '' }}">
            <a href="{{ route('dashboard-category-', 'ticket') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Ticket Categories</div>
            </a>
        </li>
        <li class="menu-item {{ Request::url() == route('dashboard-faqs-') ? 'active' : '' }}">
            <a href="{{ route('dashboard-faqs-') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="FAQ'S">FAQ'S</div>
            </a>
        </li>


    </ul>
</aside>
