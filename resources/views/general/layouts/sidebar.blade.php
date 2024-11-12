<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a class="app-brand-link">

            <span class="app-brand-text demo menu-text fw-bold"><img src="/assets/img/sideBarLogo.png"
                    alt=""></span>
        </a>

        {{-- <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
        </a> --}}
    </div>
    <div class="brandborder">

    </div>


    <ul class="menu-inner py-1">
        <!-- Dashboards -->

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Global Content</span>
        </li>

        <li class="menu-item {{ Request::url() == route('category-', 'staff-benefit') ? 'active' : '' }}">
            <a href="{{ route('category-', 'staff-benefit') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Staff Benefits</div>
            </a>
        </li>
        <li class="menu-item {{ Request::url() == route('category-', 'designation') ? 'active' : '' }}">
            <a href="{{ route('category-', 'designation') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Designation</div>
            </a>
        </li>
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Individual Content</span>
        </li>

        <li class="menu-item {{ Request::url() == route('category-', 'interest') ? 'active' : '' }}">
            <a href="{{ route('category-', 'interest') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Interest Categories</div>
            </a>
        </li>
        <li class="menu-item {{ Request::url() == route('category-', 'individual-title') ? 'active' : '' }}">
            <a href="{{ route('category-', 'individual-title') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Individual Title</div>
            </a>
        </li>

        <li
            class="menu-item {{ Request::url() == route('category-', 'healthcare-profession') ? 'active' : '' }} ||   {{ Str::contains(Request::url(), 'general/dashboard/category/sub/healthcare-specialization') ? 'active' : '' }} ||   {{ Str::contains(Request::url(), 'general/dashboard/category/sub/healthcare-sub-specialization') ? 'active' : '' }}">
            <a href="{{ route('category-', 'healthcare-profession') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Healthcare Profession</div>
            </a>
        </li>
        <li
            class="menu-item {{ Request::url() == route('category-', 'stem-profession') ? 'active' : '' }} ||   {{ Str::contains(Request::url(), 'general/dashboard/category/sub/stem-specialization') ? 'active' : '' }} ||   {{ Str::contains(Request::url(), 'general/dashboard/category/sub/stem-sub-specialization') ? 'active' : '' }}">
            <a href="{{ route('category-', 'stem-profession') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">STEM Profession</div>
            </a>
        </li>
        <li
            class="menu-item {{ Request::url() == route('category-', 'management-profession') ? 'active' : '' }} ||   {{ Str::contains(Request::url(), 'general/dashboard/category/sub/management-specialization') ? 'active' : '' }} ||   {{ Str::contains(Request::url(), 'general/dashboard/category/sub/management-sub-specialization') ? 'active' : '' }}">
            <a href="{{ route('category-', 'management-profession') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Management Profession</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Facility Content</span>
        </li>

        <li
            class="menu-item {{ Request::url() == route('category-', 'hospital-department') ? 'active' : '' }} ||   {{ Str::contains(Request::url(), 'general/dashboard/category/sub/hospital-specialization') ? 'active' : '' }} ||   {{ Str::contains(Request::url(), 'general/dashboard/category/sub/hospital-sub-specialization') ? 'active' : '' }}">
            <a href="{{ route('category-', 'hospital-department') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Hospital Department</div>
            </a>
        </li>
        <li
            class="menu-item {{ Request::url() == route('category-', 'doctor-specialization') ? 'active' : '' }} ||   {{ Str::contains(Request::url(), 'general/dashboard/category/sub/doctor-specialization') ? 'active' : '' }} ||   {{ Str::contains(Request::url(), 'general/dashboard/category/sub/doctor-sub-specialization') ? 'active' : '' }}">
            <a href="{{ route('category-', 'doctor-specialization') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Doctor Offices</div>
            </a>
        </li>
        <li
            class="menu-item {{ Request::url() == route('category-', 'elderly-care') ? 'active' : '' }} ||   {{ Str::contains(Request::url(), 'general/dashboard/category/sub/elderly-specialization') ? 'active' : '' }} ||   {{ Str::contains(Request::url(), 'general/dashboard/category/sub/elderly-sub-specialization') ? 'active' : '' }}">
            <a href="{{ route('category-', 'elderly-care') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Elderly Care</div>
            </a>
        </li>
        <li
            class="menu-item {{ Request::url() == route('category-', 'rehabilitation-specialization') ? 'active' : '' }} ||   {{ Str::contains(Request::url(), 'general/dashboard/category/sub/rehabilitation-specialization') ? 'active' : '' }} ||   {{ Str::contains(Request::url(), 'general/dashboard/category/sub/rehabilitation-sub-specialization') ? 'active' : '' }}">
            <a href="{{ route('category-', 'rehabilitation-specialization') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Rehabilitation Specialization</div>
            </a>
        </li>

        <li class="menu-item {{ Request::url() == route('category-', 'training') ? 'active' : '' }}">
            <a href="{{ route('category-', 'training') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Hospital Training Abilities</div>
            </a>
        </li>
        <li class="menu-item {{ Request::url() == route('category-', 'hospital-training-focus') ? 'active' : '' }}">
            <a href="{{ route('category-', 'hospital-training-focus') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Hospital Training Focus</div>
            </a>
        </li>
        <li
            class="menu-item {{ Request::url() == route('category-', 'hospital-training-qualification') ? 'active' : '' }}">
            <a href="{{ route('category-', 'hospital-training-qualification') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Hospital Training Qualifications</div>
            </a>
        </li>

        <li class="menu-item {{ Request::url() == route('category-', 'doctor-training') ? 'active' : '' }}">
            <a href="{{ route('category-', 'doctor-training') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Doctor Training Abilities</div>
            </a>
        </li>
        <li class="menu-item {{ Request::url() == route('category-', 'elderly-care-training') ? 'active' : '' }}">
            <a href="{{ route('category-', 'elderly-care-training') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Elderly Care Training Abilities</div>
            </a>
        </li>

       

        <li class="menu-item {{ Request::url() == route('category-', 'rehabilitation-training') ? 'active' : '' }}">
            <a href="{{ route('category-', 'rehabilitation-training') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Rehabilitation Training Abilities</div>
            </a>
        </li>

        <li
            class="menu-item {{ Request::url() == route('category-', 'rehabilitation-training-focus') ? 'active' : '' }}">
            <a href="{{ route('category-', 'rehabilitation-training-focus') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Rehabilitation Training Focus</div>
            </a>
        </li>
        <li
            class="menu-item {{ Request::url() == route('category-', 'rehabilitation-training-qualification') ? 'active' : '' }}">
            <a href="{{ route('category-', 'rehabilitation-training-qualification') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Rehabilitation Training Qualifications</div>
            </a>
        </li>
        <li class="menu-item {{ Request::url() == route('category-', 'facility-special-feature') ? 'active' : '' }}">
            <a href="{{ route('category-', 'facility-special-feature') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Facility Special Features</div>
            </a>
        </li>
        {{-- <li class="menu-item {{ Request::url() == route('category-', 'treatment-service') ? 'active' : '' }}">
            <a href="{{ route('category-', 'treatment-service') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Treatment Service</div>
            </a>
        </li> --}}

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Oragization Content</span>
        </li>

        <li
            class="menu-item {{ Request::url() == route('category-', 'association-sector') ? 'active' : '' }} ||   {{ Str::contains(Request::url(), 'general/dashboard/category/sub/association-specialization') ? 'active' : '' }} ||   {{ Str::contains(Request::url(), 'general/dashboard/category/sub/association-sub-specialization') ? 'active' : '' }}">
            <a href="{{ route('category-', 'association-sector') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Association Sector</div>
            </a>
        </li>
        <li
            class="menu-item {{ Request::url() == route('category-', 'society-sector') ? 'active' : '' }} ||   {{ Str::contains(Request::url(), 'general/dashboard/category/sub/society-specialization') ? 'active' : '' }} ||   {{ Str::contains(Request::url(), 'general/dashboard/category/sub/society-sub-specialization') ? 'active' : '' }}">
            <a href="{{ route('category-', 'society-sector') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Society Sector</div>
            </a>
        </li>
        <li
            class="menu-item {{ Request::url() == route('category-', 'company-sector') ? 'active' : '' }} ||   {{ Str::contains(Request::url(), 'general/dashboard/category/sub/company-specialization') ? 'active' : '' }} ||   {{ Str::contains(Request::url(), 'general/dashboard/category/sub/company-sub-specialization') ? 'active' : '' }}">
            <a href="{{ route('category-', 'company-sector') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Company Sector</div>
            </a>
        </li>
        <li
            class="menu-item {{ Request::url() == route('category-', 'start-sector') ? 'active' : '' }} ||   {{ Str::contains(Request::url(), 'general/dashboard/category/sub/start-specialization') ? 'active' : '' }} ||   {{ Str::contains(Request::url(), 'general/dashboard/category/sub/start-sub-specialization') ? 'active' : '' }}">
            <a href="{{ route('category-', 'start-sector') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Start Up Sector</div>
            </a>
        </li>
        <li class="menu-item {{ Request::url() == route('category-', 'organization-legal-type') ? 'active' : '' }} ">
            <a href="{{ route('category-', 'organization-legal-type') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Organization Legal Type</div>
            </a>
        </li>
        <li
            class="menu-item {{ Request::url() == route('category-', 'organization-yearly-revenue') ? 'active' : '' }} ">
            <a href="{{ route('category-', 'organization-yearly-revenue') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Organization Yearly Revenue</div>
            </a>
        </li>
        <li class="menu-item {{ Request::url() == route('category-', 'start-finance-stage') ? 'active' : '' }} ">
            <a href="{{ route('category-', 'start-finance-stage') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Start Up Finance Stage</div>
            </a>
        </li>
        <li class="menu-item {{ Request::url() == route('category-', 'start-target-group') ? 'active' : '' }} ">
            <a href="{{ route('category-', 'start-target-group') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Start Up Target Group</div>
            </a>
        </li>
        <li class="menu-item {{ Request::url() == route('category-', 'start-medical-focus') ? 'active' : '' }} ">
            <a href="{{ route('category-', 'start-medical-focus') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Start Up Medical Focus</div>
            </a>
        </li>
        <li class="menu-item {{ Request::url() == route('category-', 'start-company-feature') ? 'active' : '' }} ">
            <a href="{{ route('category-', 'start-company-feature') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Start Up Company Feature</div>
            </a>
        </li>
        <li
            class="menu-item {{ Request::url() == route('category-', 'organization-special-feature') ? 'active' : '' }}">
            <a href="{{ route('category-', 'organization-special-feature') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-circle"></i>
                <div data-i18n="Contact Us Categories">Organization Special Features</div>
            </a>
        </li>
    </ul>
</aside>
