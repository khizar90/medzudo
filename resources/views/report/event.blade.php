@extends('layouts1.base')
@section('title', 'Reported reports')
@section('main', 'Report Management')
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

            <!-- reports List Table -->
            <div class="card">
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between">
                        <h5 class="card-title mb-3">Reported Event</h5>
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



                        <table class="table border-top dataTable" id="reportsTable">
                            <thead>
                                <tr>

                                    <th>User</th>
                                    <th>Reported Event</th>
                                    <th>Media</th>
                                    <th>Reason</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="">
                                @foreach ($reports as $item)
                                    <tr>
                                        <td>{{ $item->user->username }}</td>
                                        <td>{{ $item->reported_user->title }}</td>
                                        <td><img src="{{ $item->reported_user->media }}" alt="" width="100"></td>
                                        <td>{{ $item->message }}</td>
                                        <td class="detailbtn">
                                            <a href="javascript:;" class="text-body dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-sm mx-1"></i></a>
                                            <div class="dropdown-menu dropdown-menu-end m-0">
                                                <a href="#" data-id="{{ $item->id }}"
                                                    class="dropdown-item deleteReport">Delete Report
                                                </a>
                                                <a href="#" data-id="{{ $item->id }}" data-user_id="{{ $item->reported_user->id }}"
                                                    class="dropdown-item deleteUser">Delete Event
                                                </a>


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
                                        aria-live="polite">Showing {{ $reports->firstItem() }} to
                                        {{ $reports->lastItem() }}
                                        of
                                        {{ $reports->total() }} entries</div>
                                </div>
                                <div class="col-sm-12 col-md-6">
                                    <div class="dataTables_paginate paging_simple_numbers" id="paginationLinks">
                                        @if ($reports->hasPages())
                                            {{ $reports->links('pagination::bootstrap-4') }}
                                        @endif


                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- Offcanvas to add new user -->


                <div class="modal fade" data-bs-backdrop='static' id="deleteModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                        <div class="modal-content deleteModal verifymodal">
                            <div class="modal-header">
                                <div class="modal-title" id="modalCenterTitle">Are you sure you
                                    want to delete
                                    this report?
                                </div>
                            </div>
                            <div class="modal-body">
                                <div class="body">After deleting the reports you will not see
                                    this again
                                </div>
                            </div>
                            <hr class="hr">

                            <div class="container">
                                <div class="row">
                                    <div class="first">
                                        <a href="" class="btn" data-bs-dismiss="modal"
                                            style="color: #a8aaae">Cancel</a>
                                    </div>
                                    <div class="second">
                                        <a href="" class="btn text-center" id="deleteButton">
                                            <span id="deleteText">Delete</span>
                                            <span class="align-middle" id="deleteLoader" role="status"
                                                style="display: none;">
                                                <span class="spinner-border" style="color: #ffffff" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </span>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="modal fade" data-bs-backdrop='static' id="deleteUser" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                        <div class="modal-content deleteModal verifymodal">
                            <div class="modal-header">
                                <div class="modal-title" id="modalCenterTitle">Are you sure you
                                    want to delete
                                    this event?
                                </div>
                            </div>
                            <div class="modal-body">
                                <div class="body">After deleting the event the event will not acces the app
                                </div>
                            </div>
                            <hr class="hr">

                            <div class="container">
                                <div class="row">
                                    <div class="first">
                                        <a href="" class="btn" data-bs-dismiss="modal"
                                            style="color: #a8aaae">Cancel</a>
                                    </div>
                                    <div class="second">
                                        <a href="" class="btn text-center" id="deleteButton1">
                                            <span id="deleteText1">Delete</span>
                                            <span class="align-middle" id="deleteLoader1" role="status"
                                                style="display: none;">
                                                <span class="spinner-border" style="color: #ffffff" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </span>
                                            </span>
                                        </a>
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
            $('.deleteReport').click(function(e) {
                var itemId = $(this).data('id');
                console.log(itemId)
                $('#deleteButton').attr('href', "/dashboard/report/delete" + "/" +
                    itemId);
                $('#deleteModal').modal('show');
            });
            $(document).ready(function() {
                $('#deleteButton').click(function() {
                    var deletebtntext = document.getElementById('deleteText');
                    var deleteloader = document.getElementById('deleteLoader');

                    // Hide the first div when Delete button is clicked
                    $('.first').hide();
                    // Modify the widths of the second div
                    $('.second').css('width', '100%');
                    deletebtntext.style.display = 'none';
                    deleteloader.style.display = 'block';

                });
            });
        </script>

        <script>
            $('.deleteUser').click(function(e) {
                var itemId = $(this).data('id');
                var user_id = $(this).data('user_id');
                console.log(itemId)
                $('#deleteButton1').attr('href', "/dashboard/report/event/delete" + "/" +
                    user_id + "/" + itemId);
                $('#deleteUser').modal('show');
            });
            $(document).ready(function() {
                $('#deleteButton1').click(function() {
                    var deletebtntext1 = document.getElementById('deleteText1');
                    var deleteloader1 = document.getElementById('deleteLoader1');

                    // Hide the first div when Delete button is clicked
                    $('.first').hide();
                    // Modify the widths of the second div
                    $('.second').css('width', '100%');
                    // element.classList.add('d-none');
                    deletebtntext1.style.display = 'none';
                    deleteloader1.style.display = 'block';

                });
            });
        </script>
    @endsection
