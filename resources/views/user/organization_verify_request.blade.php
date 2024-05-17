@extends('layouts1.base')
@section('title', 'Verify Users')
@section('main', 'Verify Users')
@section('link')
    <link rel="stylesheet" href="/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
    <link rel="stylesheet" href="/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
    <link rel="stylesheet" href="/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
    <link rel="stylesheet" href="/assets/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css" />
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">

            <!-- Users List Table -->
            <div class="card">
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between">
                        <h5 class="card-title mb-3">Users List</h5>
                        <div class="">
                            {{-- <button class="btn btn-primary btn-sm" id="clearFiltersBtn">Clear Filter</button> --}}
                        </div>
                    </div>



                    @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissible" role="alert">
                            {{ session()->get('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif




                </div>
                <div class="card-datatable table-responsive">
                    <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">


                        {{-- <div class="row me-2">
                            <div class="col-md-2">
                                <div class="me-3">

                                </div>
                            </div>
                            <div class="col-md-10">
                                <div
                                    class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0">
                                    <div id="DataTables_Table_0_filter" class="dataTables_filter">
                                        <label class="user_search">
                                            <input type="text" class="form-control" id="searchInput"
                                                placeholder="Search.." value="" aria-controls="DataTables_Table_0">
                                        </label>
                                    </div>

                                </div>
                            </div>
                        </div> --}}

                        <table class="table border-top dataTable" id="usersTable">
                            <thead>
                                <tr>

                                    <th>User</th>
                                    <th>username</th>
                                    <th>image</th>
                                    <th>verify</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="searchResults">
                                @foreach ($users as $user)
                                    <tr class="odd">

                                        <td class="sorting_1">
                                            <div class="d-flex justify-content-start align-items-center user-name">
                                                @if ($user->image)
                                                    <div class="avatar-wrapper">
                                                        <div class="avatar avatar-sm me-3"><img
                                                                src="{{ asset($user->image != '' ? $user->image : 'user.png') }}"
                                                                alt="Avatar" class="rounded-circle">
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="avatar-wrapper">
                                                        <div class="avatar avatar-sm me-3"><span
                                                                class="avatar-initial rounded-circle bg-label-danger">
                                                                {{ strtoupper(substr($user->username, 0, 2)) }}</span>
                                                        </div>
                                                    </div>
                                                @endif



                                                <div class="d-flex flex-column"><a href=""
                                                        class="text-body text-truncate"><span
                                                            class="fw-semibold user-name-text">{{ $user->username }}</span></a><small
                                                        class="text-muted">&#64;{{ $user->email }}</small>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="user-category">{{ $user->username }}</td>

                                        <td class="user-category">
                                            <img src="{{ $user->request_image }}" class="rounded object-fit-cover"
                                                alt="" width="200" height="100">
                                        </td>

                                        <td class="account-status text-start">

                                            <button class="badge bg-label-secondary btn" data-bs-toggle="modal"
                                                data-bs-target="#verifyModal{{ $user->uuid }}" text-capitalized="">Pending

                                            </button>


                                            <div class="modal fade" data-bs-backdrop='static'
                                                id="verifyModal{{ $user->uuid }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                                    <div class="modal-content verifymodal">
                                                        <div class="modal-header">
                                                            <div class="modal-title" id="modalCenterTitle">Are you
                                                                sure you want to approve
                                                                this account?
                                                            </div>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="body">If you will approve this account after
                                                                that
                                                                this user will access medzudo app.</div>
                                                        </div>
                                                        <hr class="hr">

                                                        <div class="container">
                                                            <div class="row">
                                                                <div class="first">
                                                                    <a href="" class="btn" data-bs-dismiss="modal"
                                                                        style="color: #a8aaae ">Cancel</a>
                                                                </div>
                                                                <div class="second">
                                                                    <a class="btn text-center"
                                                                        href="{{ route('dashboard-get-verify', ['type' => 'organization', 'id' => $user->uuid]) }}">VERIFY</a>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </td>





                                        <td class="" style="">
                                            <div class="d-flex align-items-center">


                                                <a href="" data-bs-toggle="modal"
                                                    data-bs-target="#deleteModal{{ $user->uuid }}"
                                                    class="text-body delete-record">
                                                    <i class="ti ti-trash x`ti-sm mx-2"></i>
                                                </a>




                                            </div>


                                            <div class="modal fade" data-bs-backdrop='static'
                                                id="deleteModal{{ $user->uuid }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                                    <div class="modal-content deleteModal verifymodal">
                                                        <div class="modal-header">
                                                            <div class="modal-title" id="modalCenterTitle">Are you
                                                                sure you want to delete
                                                                this account?
                                                            </div>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="body">After delete this account user cannot
                                                                access anything in application</div>
                                                        </div>
                                                        <hr class="hr">

                                                        <div class="container">
                                                            <div class="row">
                                                                <div class="first">
                                                                    <a href="" class="btn"
                                                                        data-bs-dismiss="modal"
                                                                        style="color: #a8aaae ">Cancel</a>
                                                                </div>
                                                                <div class="second">
                                                                    <a class="btn text-center"
                                                                        href="{{ url('admin/user/delete', $user->uuid) }}">Delete</a>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach



                            </tbody>
                        </table>


                        <div id="paginationContainer">
                            <div class="row mx-2">
                                <div class="col-sm-12 col-md-6">
                                    <div class="dataTables_info" id="DataTables_Table_0_info" role="status"
                                        aria-live="polite">Showing {{ $users->firstItem() }} to
                                        {{ $users->lastItem() }}
                                        of
                                        {{ $users->total() }} entries</div>
                                </div>
                                <div class="col-sm-12 col-md-6">
                                    <div class="dataTables_paginate paging_simple_numbers" id="paginationLinks">
                                        {{-- <h1>{{ @json($data) }}</h1> --}}
                                        @if ($users->hasPages())
                                            {{ $users->links('pagination::bootstrap-4') }}
                                        @endif


                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('script')
        <script>
            $(document).ready(function() {
                $('#searchInput').keyup(function() {
                    var searchValue = $(this).val();
                    var loader = $('#loader');
                    loader.show();


                    // if (searchValue.length) { // Adjust the minimum length as needed
                    $.ajax({
                        url: '{{ route('dashboard-verify-users') }}', // Replace with your controller route
                        method: 'GET',
                        data: {
                            query: searchValue
                        },
                        success: function(data) {
                            console.log(data);
                            $("#searchResults").html(data)

                        },
                        complete: function() {
                            loader.hide(); // Hide the loader after request is complete
                        }
                    });
                    // }
                });
            });
        </script>
    @endsection
