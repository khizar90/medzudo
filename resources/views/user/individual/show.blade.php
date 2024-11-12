@extends('layouts1.base')
@section('title', 'User Profile')
@section('main', 'User Profile')
@section('link')
    <link rel="stylesheet" href="/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="/assets/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
    <link rel="stylesheet" href="/assets/vendor/css/pages/page-profile.css" />
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">
            {{-- <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">User Profile /</span> Profile</h4> --}}

            <!-- Header -->
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="user-profile-header-banner">
                            <img src="/profile-banner.jpg" alt="Banner image" class="rounded-top" style="width: 100%" />
                        </div>
                        <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4">
                            <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
                                <img src="{{ $user->image != '' ? 'https://d38vqtrl6p25ob.cloudfront.net/' . $user->image : asset('placeholder.png') }}"
                                    alt="user image" class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img"
                                    width="100" id="image-preview"
                                    onclick="{{ $user->image != '' ? 'openModal()' : '' }}" />
                            </div>
                            <div class="flex-grow-1 mt-3 mt-sm-5">
                                <div
                                    class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4">
                                    <div class="user-profile-info">
                                        <h4>{{ $user->first_name }} {{ $user->last_name }}</h4>
                                        <ul
                                            class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2">
                                            {{-- <li class="list-inline-item d-flex gap-1">
                                                <i class="ti ti-color-swatch"></i> {{ $user->language }}
                                            </li>
                                            <li class="list-inline-item d-flex gap-1">
                                                <i class="ti ti-phone"></i> #{{ $user->number }}
                                            </li> --}}
                                            <li class="list-inline-item d-flex gap-1">
                                                <i class="ti ti-calendar"></i> Joined
                                                {{ $user->created_at->format('d F, Y \a\t h:i A') }}
                                            </li>
                                        </ul>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xl-4 col-lg-5 col-md-5">
                    <!-- About User -->
                    <div class="card mb-6">
                        <div class="card-body">
                            <small class="card-text text-uppercase text-muted small">About</small>
                            <ul class="list-unstyled my-3 py-1">
                                <li class="d-flex align-items-center mb-4"><i class="ti ti-user ti-lg"></i><span
                                        class="fw-medium mx-2">Full Name:</span> <span>{{ $user->first_name }}
                                        {{ $user->last_name }}</span></li>
                                <li class="d-flex align-items-center mb-4"><i class="ti ti-mail ti-lg"></i><span
                                        class="fw-medium mx-2">Email:</span> <span>{{ $user->email }}</span></li>


                                <li class="d-flex align-items-center mb-4"><i class="ti ti-user-check ti-lg"></i><span
                                        class="fw-medium mx-2">Username:</span> <span>{{ $user->username }}</span></li>
                                <li class="d-flex align-items-center mb-4"><i class="ti ti-article ti-lg"></i><span
                                        class="fw-medium mx-2">Title:</span> <span>{{ $user->title }}</span></li>
                                <li class="d-flex align-items-center mb-4"><i class="ti ti-phone-call ti-lg"></i><span
                                        class="fw-medium mx-2">Phone:</span> <span>{{ $user->phone_number }}</span></li>

                                <li class="d-flex align-items-center">
                                    <i class="ti ti-check ti-lg"></i>
                                    <span class="fw-medium mx-2">Status:</span>
                                    <span>
                                        @if ($user->verify == 0)
                                            Not Yet Applied
                                        @elseif($user->verify == 1)
                                            Verified User
                                        @else
                                            Pending
                                        @endif
                                    </span>
                                </li>

                            </ul>

                        </div>
                    </div>
                    <!--/ About User -->
                    <!-- Profile Overview -->
                    <div class="card mb-6 mt-3">
                        <div class="card-body">
                            <small class="card-text text-uppercase text-muted small">Overview</small>
                            <ul class="list-unstyled mb-0 mt-3 pt-1">
                                <li class="d-flex align-items-end mb-4"><i class="ti ti-check ti-lg"></i><span
                                        class="fw-medium mx-2">Follwers:</span> <span>{{ $user->follower }}</span></li>
                                <li class="d-flex align-items-end mb-4"><i class="ti ti-layout-grid ti-lg"></i><span
                                        class="fw-medium mx-2">Followings:</span> <span>{{ $user->following }}</span></li>
                                <li class="d-flex align-items-end"><i class="ti ti-users ti-lg"></i><span
                                        class="fw-medium mx-2">Communities:</span> <span>{{ $user->communities }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!--/ Profile Overview -->
                </div>
                <div class="col-xl-8 col-lg-7 col-md-7">
                    <div class="card card-action mb-6">

                        <div class="card-body pt-3">
                            <small class="card-text text-uppercase text-muted small">Profession</small>
                            <ul class="list-unstyled my-3 py-1">
                                <li class="d-flex align-items-center mb-4"><i class="ti ti-crown ti-lg"></i><span
                                        class="fw-medium mx-2">Position:</span> <span>{{ $user->position }}</span></li>
                                <li class="d-flex align-items-center mb-4"><i class="ti ti-vector ti-lg"></i><span
                                        class="fw-medium mx-2">Sector:</span> <span>{{ $user->sector }}</span></li>
                                <li class="d-flex align-items-center mb-4"><i class="ti ti-player-pause ti-lg"></i><span
                                        class="fw-medium mx-2">Employer:</span> <span>{{ $user->employer }}</span></li>
                                <li class="d-flex align-items-center mb-4"><i class="ti ti-cross ti-lg"></i><span
                                        class="fw-medium mx-2">Profession:</span> <span>{{ $user->professionName }}</span>
                                </li>
                                <li class="d-flex align-items-center mb-4"><i class="ti ti-aspect-ratio ti-lg"></i><span
                                        class="fw-medium mx-2">Specialization:</span>
                                    <span>{{ $user->specializationName }}</span>
                                </li>
                                <li class="d-flex align-items-center mb-4"><i class="ti ti-aspect-ratio ti-lg"></i><span
                                        class="fw-medium mx-2">Sub Specialization:</span>
                                    <span>{{ $user->subSpecializationName }}</span>
                                </li>
                                <li class="d-flex align-items-center mb-4"><i class="ti ti-cursor-off ti-lg"></i><span
                                        class="fw-medium mx-2">Assosiation Membership:</span>
                                    <span>{{ $user->association_memberships }}</span>
                                </li>
                                <li class="d-flex align-items-center mb-4"><i class="ti ti-flask ti-lg"></i><span
                                        class="fw-medium mx-2">Experience:</span>
                                    <span>{{ $user->experience }}</span>
                                </li>


                            </ul>
                            <small class="card-text text-uppercase text-muted small">Location</small>
                            <ul class="list-unstyled mb-0 mt-3 pt-1 mb-3">
                                <li class="d-flex flex-wrap">
                                    <span class="fw-medium me-2"></span><span>{{ $user->location }}</span>
                                </li>
                            </ul>
                            <small class="card-text text-uppercase text-muted small">About</small>
                            <ul class="list-unstyled mb-0 mt-3 pt-1">
                                <li class="d-flex flex-wrap">
                                    <span class="fw-medium me-2"></span><span>{{ $user->about }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card card-action mb-6 mt-3">
                    </div>
                
                    {{-- <div class="row">
                        <div class="col-lg-12 col-xl-6">
                            <div class="card card-action mb-6">
                                <div class="card-header align-items-center">
                                    <h5 class="card-action-title mb-0">Connections</h5>
                                    <div class="card-action-element">
                                        <div class="dropdown">
                                            <button type="button"
                                                class="btn btn-icon btn-text-secondary rounded-pill dropdown-toggle hide-arrow p-0 text-muted waves-effect waves-light"
                                                data-bs-toggle="dropdown" aria-expanded="false"><i
                                                    class="ti ti-dots-vertical ti-md text-muted"></i></button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item waves-effect" href="javascript:void(0);">Share
                                                        connections</a></li>
                                                <li><a class="dropdown-item waves-effect"
                                                        href="javascript:void(0);">Suggest edits</a></li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li><a class="dropdown-item waves-effect"
                                                        href="javascript:void(0);">Report bug</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-4">
                                            <div class="d-flex align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar me-2">
                                                        <img src="../../assets/img/avatars/2.png" alt="Avatar"
                                                            class="rounded-circle">
                                                    </div>
                                                    <div class="me-2">
                                                        <h6 class="mb-0">Cecilia Payne</h6>
                                                        <small>45 Connections</small>
                                                    </div>
                                                </div>
                                                <div class="ms-auto">
                                                    <button class="btn btn-label-primary btn-icon waves-effect"><i
                                                            class="ti ti-user-check ti-md"></i></button>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="mb-4">
                                            <div class="d-flex align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar me-2">
                                                        <img src="../../assets/img/avatars/3.png" alt="Avatar"
                                                            class="rounded-circle">
                                                    </div>
                                                    <div class="me-2">
                                                        <h6 class="mb-0">Curtis Fletcher</h6>
                                                        <small>1.32k Connections</small>
                                                    </div>
                                                </div>
                                                <div class="ms-auto">
                                                    <button class="btn btn-primary btn-icon waves-effect waves-light"><i
                                                            class="ti ti-user-x ti-md"></i></button>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="mb-4">
                                            <div class="d-flex align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar me-2">
                                                        <img src="../../assets/img/avatars/10.png" alt="Avatar"
                                                            class="rounded-circle">
                                                    </div>
                                                    <div class="me-2">
                                                        <h6 class="mb-0">Alice Stone</h6>
                                                        <small>125 Connections</small>
                                                    </div>
                                                </div>
                                                <div class="ms-auto">
                                                    <button class="btn btn-primary btn-icon waves-effect waves-light"><i
                                                            class="ti ti-user-x ti-md"></i></button>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="mb-4">
                                            <div class="d-flex align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar me-2">
                                                        <img src="../../assets/img/avatars/7.png" alt="Avatar"
                                                            class="rounded-circle">
                                                    </div>
                                                    <div class="me-2">
                                                        <h6 class="mb-0">Darrell Barnes</h6>
                                                        <small>456 Connections</small>
                                                    </div>
                                                </div>
                                                <div class="ms-auto">
                                                    <button class="btn btn-label-primary btn-icon waves-effect"><i
                                                            class="ti ti-user-check ti-md"></i></button>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="mb-6">
                                            <div class="d-flex align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar me-2">
                                                        <img src="../../assets/img/avatars/12.png" alt="Avatar"
                                                            class="rounded-circle">
                                                    </div>
                                                    <div class="me-2">
                                                        <h6 class="mb-0">Eugenia Moore</h6>
                                                        <small>1.2k Connections</small>
                                                    </div>
                                                </div>
                                                <div class="ms-auto">
                                                    <button class="btn btn-label-primary btn-icon waves-effect"><i
                                                            class="ti ti-user-check ti-md"></i></button>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="text-center">
                                            <a href="javascript:;">View all connections</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                       
                        <div class="col-lg-12 col-xl-6">
                            <div class="card card-action mb-6">
                                <div class="card-header align-items-center">
                                    <h5 class="card-action-title mb-0">Teams</h5>
                                    <div class="card-action-element">
                                        <div class="dropdown">
                                            <button type="button"
                                                class="btn btn-icon btn-text-secondary dropdown-toggle hide-arrow p-0 waves-effect waves-light"
                                                data-bs-toggle="dropdown" aria-expanded="false"><i
                                                    class="ti ti-dots-vertical text-muted"></i></button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item waves-effect" href="javascript:void(0);">Share
                                                        teams</a></li>
                                                <li><a class="dropdown-item waves-effect"
                                                        href="javascript:void(0);">Suggest edits</a></li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li><a class="dropdown-item waves-effect"
                                                        href="javascript:void(0);">Report bug</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-4">
                                            <div class="d-flex align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar me-2">
                                                        <img src="../../assets/img/icons/brands/react-label.png"
                                                            alt="Avatar" class="rounded-circle">
                                                    </div>
                                                    <div class="me-2">
                                                        <h6 class="mb-0">React Developers</h6>
                                                        <small>72 Members</small>
                                                    </div>
                                                </div>
                                                <div class="ms-auto">
                                                    <a href="javascript:;"><span
                                                            class="badge bg-label-danger">Developer</span></a>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="mb-4">
                                            <div class="d-flex align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar me-2">
                                                        <img src="../../assets/img/icons/brands/support-label.png"
                                                            alt="Avatar" class="rounded-circle">
                                                    </div>
                                                    <div class="me-2">
                                                        <h6 class="mb-0">Support Team</h6>
                                                        <small>122 Members</small>
                                                    </div>
                                                </div>
                                                <div class="ms-auto">
                                                    <a href="javascript:;"><span
                                                            class="badge bg-label-primary">Support</span></a>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="mb-4">
                                            <div class="d-flex align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar me-2">
                                                        <img src="../../assets/img/icons/brands/figma-label.png"
                                                            alt="Avatar" class="rounded-circle">
                                                    </div>
                                                    <div class="me-2">
                                                        <h6 class="mb-0">UI Designers</h6>
                                                        <small>7 Members</small>
                                                    </div>
                                                </div>
                                                <div class="ms-auto">
                                                    <a href="javascript:;"><span
                                                            class="badge bg-label-info">Designer</span></a>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="mb-4">
                                            <div class="d-flex align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar me-2">
                                                        <img src="../../assets/img/icons/brands/vue-label.png"
                                                            alt="Avatar" class="rounded-circle">
                                                    </div>
                                                    <div class="me-2">
                                                        <h6 class="mb-0">Vue.js Developers</h6>
                                                        <small>289 Members</small>
                                                    </div>
                                                </div>
                                                <div class="ms-auto">
                                                    <a href="javascript:;"><span
                                                            class="badge bg-label-danger">Developer</span></a>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="mb-6">
                                            <div class="d-flex align-items-center">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar me-2">
                                                        <img src="../../assets/img/icons/brands/twitter-label.png"
                                                            alt="Avatar" class="rounded-circle">
                                                    </div>
                                                    <div class="me-w">
                                                        <h6 class="mb-0">Digital Marketing</h6>
                                                        <small>24 Members</small>
                                                    </div>
                                                </div>
                                                <div class="ms-auto">
                                                    <a href="javascript:;"><span
                                                            class="badge bg-label-secondary">Marketing</span></a>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="text-center">
                                            <a href="javascript:;">View all teams</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div> --}}

                </div>
                <div class="col-xl-12 col-lg-12 col-md-12">
                    <div class="card card-action mb-6 mt-3">
                        <div class="card-body pt-3">
                            <div class="col-xl-12">
                                <h6 class="text-muted">Detail</h6>
                                <div class="nav-align-top mb-6">
                                    <ul class="nav nav-pills mb-4" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button type="button" class="nav-link waves-effect waves-light active"
                                                role="tab" data-bs-toggle="tab" data-bs-target="#navs-pills-top-home"
                                                aria-controls="navs-pills-top-home" aria-selected="true">Links</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button type="button" class="nav-link waves-effect waves-light"
                                                role="tab" data-bs-toggle="tab"
                                                data-bs-target="#navs-pills-top-profile"
                                                aria-controls="navs-pills-top-profile" aria-selected="false"
                                                tabindex="-1">Education</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button type="button" class="nav-link waves-effect waves-light"
                                                role="tab" data-bs-toggle="tab"
                                                data-bs-target="#navs-pills-top-messages"
                                                aria-controls="navs-pills-top-messages" aria-selected="false"
                                                tabindex="-1">Experience</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button type="button" class="nav-link waves-effect waves-light"
                                                role="tab" data-bs-toggle="tab"
                                                data-bs-target="#certification"
                                                aria-controls="certification" aria-selected="false"
                                                tabindex="-1">Certification</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button type="button" class="nav-link waves-effect waves-light"
                                                role="tab" data-bs-toggle="tab"
                                                data-bs-target="#publication"
                                                aria-controls="publication" aria-selected="false"
                                                tabindex="-1">Publication</button>
                                        </li>
                                    </ul>
                                    <div class="tab-content tabButtonDiv">
                                        <div class="tab-pane fade active show" id="navs-pills-top-home" role="tabpanel">
                                            <small class="card-text text-uppercase text-muted small">Links</small>
                                            <ul class="list-unstyled mb-0 mt-3 pt-1">
                                                <li class="d-flex align-items-end mb-4"><i
                                                        class="ti ti-brand-instagram ti-lg"></i><span
                                                        class="fw-medium mx-2">Instagram:</span>
                                                    <span>{{ $user->instagram_link }}</span>
                                                </li>
                                                <li class="d-flex align-items-end mb-4"><i
                                                        class="ti ti-brand-linkedin ti-lg"></i><span
                                                        class="fw-medium mx-2">LinkedIn:</span>
                                                    <span>{{ $user->linkedin_link }}</span>
                                                </li>

                                                <li class="d-flex align-items-end mb-4"><i
                                                        class="ti ti-brand-youtube ti-lg"></i><span
                                                        class="fw-medium mx-2">Youtube:</span>
                                                    <span>{{ $user->youtube_link }}</span></li>

                                                <li class="d-flex align-items-end mb-4"><i
                                                        class="ti ti-brand-twitter ti-lg"></i><span
                                                        class="fw-medium mx-2">X:</span> <span>{{ $user->x_link }}</span>
                                                </li>
                                                <li class="d-flex align-items-end mb-4"><i
                                                        class="ti ti-sitemap ti-lg"></i><span
                                                        class="fw-medium mx-2">Website:</span>
                                                    <span>{{ $user->linkedin_link }}</span>
                                                </li>

                                                <li class="d-flex align-items-end"><i
                                                        class="ti ti-brand-facebook ti-lg"></i><span
                                                        class="fw-medium mx-2">Facebook:</span>
                                                    <span>{{ $user->facebook_link }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="tab-pane fade" id="navs-pills-top-profile" role="tabpanel">
                                            <ul class="timeline mb-0">
                                                @foreach ($user->all_education as $education)
                                                    <li class="timeline-item timeline-item-transparent">
                                                        <span class="timeline-point timeline-point-primary"></span>
                                                        <div class="timeline-event">
                                                            <div class="timeline-header mb-3">
                                                                <h6 class="mb-0">{{ $education->name }}</h6>
                                                                {{-- <small class="text-muted">12 min ago</small> --}}
                                                            </div>
                                                            <p class="mb-2">
                                                                {{ $education->title }}
                                                            </p>
                                                            <div class="d-flex align-items-center mb-2">
                                                                @if ($education->end_year)
                                                                    <span
                                                                        class="h6 mb-0 text-body">{{ $education->start_year }}-{{ $education->end_year }}</span>
                                                                @else
                                                                    <span class="h6 mb-0 text-body">{{ $education->start_year }}-in
                                                                        progress</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="tab-pane fade" id="navs-pills-top-messages" role="tabpanel">
                                            <ul class="timeline mb-0">
                                                @foreach ($user->all_experience as $experience)
                                                    <li class="timeline-item timeline-item-transparent">
                                                        <span class="timeline-point timeline-point-primary"></span>
                                                        <div class="timeline-event">
                                                            <div class="timeline-header mb-3">
                                                                <h6 class="mb-0">{{ $experience->name }}</h6>
                                                                {{-- <small class="text-muted">12 min ago</small> --}}
                                                            </div>
                                                            <p class="mb-2">
                                                                {{ $experience->title }}
                                                            </p>
                                                            <div class="d-flex align-items-center mb-2">
                                                                @if ($experience->end_year)
                                                                    <span
                                                                        class="h6 mb-0 text-body">{{ $experience->start_year }}-{{ $experience->end_year }}</span>
                                                                @else
                                                                    <span class="h6 mb-0 text-body">{{ $experience->start_year }}-in
                                                                        progress</span>
                                                                @endif
                                                            </div>
                                                        </div>
                
                                                    </li>
                                                @endforeach
                
                                            </ul>
                                        </div>
                                        <div class="tab-pane fade" id="certification" role="tabpanel">
                                            <ul class="timeline mb-0">
                                                @foreach ($user->all_certification as $certification)
                                                    <li class="timeline-item timeline-item-transparent">
                                                        <span class="timeline-point timeline-point-primary"></span>
                                                        <div class="timeline-event">
                                                            <div class="timeline-header mb-3">
                                                                <h6 class="mb-0">{{ $certification->name }}</h6>
                                                                {{-- <small class="text-muted">12 min ago</small> --}}
                                                            </div>
                                                            <p class="mb-2">
                                                                {{ $certification->title }}
                                                            </p>
                                                            <div class="d-flex align-items-center mb-2">
                                                                @if ($certification->end_year)
                                                                    <span
                                                                        class="h6 mb-0 text-body">{{ $certification->start_year }}-{{ $certification->end_year }}</span>
                                                                @else
                                                                    <span class="h6 mb-0 text-body">{{ $certification->start_year }}-in
                                                                        progress</span>
                                                                @endif
                                                            </div>
                                                        </div>
                
                                                    </li>
                                                @endforeach
                
                                            </ul>
                                        </div>
                                        <div class="tab-pane fade" id="publication" role="tabpanel">
                                            <ul class="timeline mb-0">
                                                @foreach ($user->all_publication as $publication)
                                                    <li class="timeline-item timeline-item-transparent">
                                                        <span class="timeline-point timeline-point-primary"></span>
                                                        <div class="timeline-event">
                                                            <div class="timeline-header mb-3">
                                                                <h6 class="mb-0">{{ $publication->name }}</h6>
                                                                {{-- <small class="text-muted">12 min ago</small> --}}
                                                            </div>
                                                            <p class="mb-2">
                                                                {{ $publication->title }}
                                                            </p>
                                                            <div class="d-flex align-items-center mb-2">
                                                                @if ($publication->end_year)
                                                                    <span
                                                                        class="h6 mb-0 text-body">{{ $publication->start_year }}-{{ $publication->end_year }}</span>
                                                                @else
                                                                    <span class="h6 mb-0 text-body">{{ $publication->start_year }}-in
                                                                        progress</span>
                                                                @endif
                                                            </div>
                                                        </div>
                
                                                    </li>
                                                @endforeach
                
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>


        <div class="modal fade imageModal" id="imageModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog  modal-dialog-centered modal-simple modal-upgrade-plan modal-lg">
                <div class="modal-content  p-0 bg-transparent">
                    <div class="modal-body p-0">
                        <img id="selected-image" src="#" style="width: 100%">
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('script')
        <script src="/assets/js/pages-profile.js"></script>

        <script>
            $('#image-preview').on('click', function() {
                var imageUrl = $('#image-preview').attr('src');
                $('#selected-image').attr('src', imageUrl);
                $('#imageModal').modal('show');
            });
        </script>

        <script>
            $(document).on('click', '.nav-link', function(e) {
                e.preventDefault();
                let type = $(this).data('type');
                var loader = $('#spinner');
                loader.show();
                let userId = '{{ $user->uuid }}'

                $('#tabbutton').hide();
                $.ajax({
                    type: 'GET',
                    url: '/dashboard/users/show/' + userId,
                    data: {
                        type: type
                    },
                    success: function(response) {
                        $("#tabbutton").html(response);
                        loader.hide();
                        $('#tabbutton').show();



                    },
                    error: function(error) {
                        console.error('Error:', error);
                    }
                });
            });
        </script>

    @endsection
