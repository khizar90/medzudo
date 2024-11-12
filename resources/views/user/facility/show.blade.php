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
                                        class="fw-medium mx-2">Type:</span> <span>{{ $user->type }}</span></li>
                                <li class="d-flex align-items-center mb-4"><i class="ti ti-phone-call ti-lg"></i><span
                                        class="fw-medium mx-2">Number of Beds:</span> <span>{{ $user->no_of_bed }}</span>
                                </li>

                                <li class="d-flex align-items-center">
                                    <i class="ti ti-check ti-lg"></i>
                                    <span class="fw-medium mx-2">Staff Number:</span>
                                    <span>
                                        {{ $user->no_of_employe }}
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
                                <li class="d-flex align-items-center mb-4"><i class="ti ti-crown ti-lg"></i><span
                                        class="fw-medium mx-2">Training Abilities:</span>
                                    <span>{{ $user->trainingName }}</span>
                                </li>
                                <li class="d-flex align-items-center mb-4"><i class="ti ti-vector ti-lg"></i><span
                                        class="fw-medium mx-2">Training Abilities Focus :</span>
                                    <span>{{ $user->trainingFocusName }}</span>
                                </li>

                                <li class="d-flex align-items-center mb-4"><i class="ti ti-cursor-off ti-lg"></i><span
                                        class="fw-medium mx-2">Training Abilities Additional Qualifications :</span>
                                    <span>{{ $user->trainingQualificationName }}</span>
                                </li>
                                <li class="d-flex align-items-center mb-4"><i class="ti ti-flask ti-lg"></i><span
                                        class="fw-medium mx-2">Carrier:</span>
                                    <span>{{ $user->carrier }}</span>
                                </li>
                                <li class="d-flex align-items-center mb-4"><i class="ti ti-flask ti-lg"></i><span
                                        class="fw-medium mx-2">Institution Number:</span>
                                    <span>{{ $user->institution_number }}</span>
                                </li>
                                <li class="d-flex align-items-center mb-4"><i class="ti ti-flask ti-lg"></i><span
                                        class="fw-medium mx-2">Staff Benefits:
                                    </span>
                                    <span>{{ $user->staffBenefitName }}</span>
                                </li>
                                <li class="d-flex align-items-center mb-4"><i class="ti ti-flask ti-lg"></i><span
                                        class="fw-medium mx-2">Special Features:
                                    </span>
                                    <span>{{ $user->specialFeatureName }}</span>
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
                                                tabindex="-1">Departments</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button type="button" class="nav-link waves-effect waves-light"
                                                role="tab" data-bs-toggle="tab"
                                                data-bs-target="#navs-pills-top-messages"
                                                aria-controls="navs-pills-top-messages" aria-selected="false"
                                                tabindex="-1">Management</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button type="button" class="nav-link waves-effect waves-light"
                                                role="tab" data-bs-toggle="tab" data-bs-target="#certification"
                                                aria-controls="certification" aria-selected="false"
                                                tabindex="-1">Contact</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button type="button" class="nav-link waves-effect waves-light"
                                                role="tab" data-bs-toggle="tab" data-bs-target="#publication"
                                                aria-controls="publication" aria-selected="false"
                                                tabindex="-1">Teams</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button type="button" class="nav-link waves-effect waves-light"
                                                role="tab" data-bs-toggle="tab" data-bs-target="#media"
                                                aria-controls="media" aria-selected="false" tabindex="-1">Media</button>
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
                                                    <span>{{ $user->youtube_link }}</span>
                                                </li>

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
                                                @foreach ($user->departments as $department)
                                                    <li class="timeline-item timeline-item-transparent">
                                                        <span class="timeline-point timeline-point-primary"></span>
                                                        <div class="timeline-event">
                                                            <div class="timeline-header mb-3">
                                                                <h6 class="mb-0">{{ $department->name }}</h6>
                                                                {{-- <small class="text-muted">12 min ago</small> --}}
                                                            </div>
                                                            <ul class="list-unstyled mb-0">

                                                                @foreach ($department->department_users as $department_user)
                                                                    <li class="mb-4">
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="d-flex align-items-center">
                                                                                <div class="avatar me-2">
                                                                                    <img src="{{ 'https://d38vqtrl6p25ob.cloudfront.net/' . $department_user->user->image }}"
                                                                                        alt="Avatar"
                                                                                        class="rounded-circle">
                                                                                </div>
                                                                                <div class="me-2">
                                                                                    <h6 class="mb-0">
                                                                                        {{ $department_user->user->first_name }}
                                                                                        {{ $department_user->user->last_name }}
                                                                                    </h6>
                                                                                    <small>{{ $department_user->user->email }}</small>
                                                                                    <small>({{ $department_user->designation }})</small>

                                                                                </div>
                                                                            </div>

                                                                            <div class="ms-auto">
                                                                                <h6>{{ $department_user->departmentName }}
                                                                                </h6>

                                                                            </div>
                                                                        </div>
                                                                    </li>
                                                                @endforeach
                                                            </ul>

                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="tab-pane fade" id="navs-pills-top-messages" role="tabpanel">
                                            <ul class="timeline mb-0">
                                                @foreach ($user->management as $management)
                                                    <li class="timeline-item timeline-item-transparent">
                                                        <span class="timeline-point timeline-point-primary"></span>
                                                        <div class="timeline-event">
                                                            <div class="d-flex align-items-center">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="avatar me-2">
                                                                        <img src="{{ 'https://d38vqtrl6p25ob.cloudfront.net/' . $management->user->image }}"
                                                                            alt="" class="rounded-circle">
                                                                    </div>
                                                                    <div class="me-2">
                                                                        <h6 class="mb-0">
                                                                            {{ $management->user->first_name }}
                                                                            {{ $management->user->last_name }}
                                                                        </h6>
                                                                        <small>{{ $management->user->email }}</small>

                                                                    </div>
                                                                </div>
                                                                <div class="ms-auto">
                                                                    <h6>{{ $management->designation }}
                                                                    </h6>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="tab-pane fade" id="certification" role="tabpanel">
                                            <ul class="timeline mb-0">
                                                @foreach ($user->contact as $contact)
                                                    <li class="timeline-item timeline-item-transparent mb-4">
                                                        <span class="timeline-point timeline-point-primary"></span>
                                                        <div class="d-flex align-items-center">
                                                            <div class="d-flex align-items-center">
                                                                <div class="avatar me-2">
                                                                    <img src="{{ 'https://d38vqtrl6p25ob.cloudfront.net/' . $contact->image }}"
                                                                        alt="" class="rounded-circle">
                                                                </div>
                                                                <div class="me-2">
                                                                    <h6 class="mb-0">
                                                                        {{ $contact->name }}

                                                                    </h6>
                                                                    <small>{{ $contact->email }}</small>
                                                                    <small>({{ $contact->phone_number }})</small>

                                                                </div>
                                                            </div>
                                                            <div class="ms-auto">
                                                                <h6>{{ $contact->designation }}
                                                                </h6>

                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach

                                            </ul>
                                        </div>
                                        <div class="tab-pane fade" id="publication" role="tabpanel">
                                            <ul class="timeline mb-0">
                                                @foreach ($user->teams as $team)
                                                    <li class="timeline-item timeline-item-transparent">
                                                        <span class="timeline-point timeline-point-primary"></span>
                                                        <div class="timeline-event">
                                                            <div class="d-flex align-items-center">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="avatar me-2">
                                                                        <img src="{{ 'https://d38vqtrl6p25ob.cloudfront.net/' . $team->user->image }}"
                                                                            alt="" class="rounded-circle">
                                                                    </div>
                                                                    <div class="me-2">
                                                                        <h6 class="mb-0">
                                                                            {{ $team->user->first_name }}
                                                                            {{ $team->user->last_name }}
                                                                        </h6>
                                                                        <small>{{ $team->user->email }}</small>

                                                                    </div>
                                                                </div>
                                                                <div class="ms-auto">
                                                                    <h6>{{ $team->designation }}
                                                                    </h6>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="tab-pane fade" id="media" role="tabpanel">
                                            <h6>Images</h6>

                                            <div class="row mb-4">
                                                @foreach ($user->media as $media)
                                                    @if ($media->type == 'image')
                                                        <div class="col-md-2 mediaImage">
                                                            <a href="{{ 'https://d38vqtrl6p25ob.cloudfront.net/' . $media->media }}"
                                                                onclick="window.open(this.href); return false;">
                                                                <img src="{{ 'https://d38vqtrl6p25ob.cloudfront.net/' . $media->media }}"
                                                                    alt="">
                                                            </a>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                            <h6>Video</h6>

                                            <div class="row mb-4">
                                                @foreach ($user->media as $media)
                                                    @if ($media->type == 'video')
                                                        <div class="col-md-2 mediaImage">
                                                            {{-- <a href="{{ 'https://d38vqtrl6p25ob.cloudfront.net/' . $media->media }}" --}}
                                                                {{-- onclick="window.open(this.href); return false;"> --}}
                                                                <video controls 
                                                                    src="{{ 'https://d38vqtrl6p25ob.cloudfront.net/' . $media->media }}"></video>
                                                            {{-- </a> --}}
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>

                                            <h6>Link</h6>
                                            <div class="row mb-4">
                                                @foreach ($user->media as $media)
                                                    @if ($media->type == 'url')
                                                      <a href="{{ $media->media }}">{{ $media->media }}</a>
                                                    @endif
                                                @endforeach
                                            </div>
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
