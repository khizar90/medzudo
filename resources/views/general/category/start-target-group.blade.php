@extends('general.layouts.base')
@section('title', 'Target group ')
@section('main', 'Target group  Management')
@section('link')
    <link rel="stylesheet" href="/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">

            <!-- Users List Table -->
            <div class="card">
                <div class="card-header border-bottom">
                    <div class="d-flex justify-content-between">
                        <h5 class="card-title mb-3">Target group </h5>
                        <div class="">
                            <button class="btn btn-secondary add-new btn-primary" tabindex="0"
                                aria-controls="DataTables_Table_0" type="button" data-bs-toggle="modal"
                                data-bs-target="#addNewBus"><span><i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span
                                        class="d-none d-sm-inline-block">Add New Target group </span></span></button>
                        </div>
                    </div>
                   
                </div>
                <div class="card-datatable table-responsive">
                    <div class="row mt-3 mb-3">
                        <div class="col-md-2">

                        </div>
                        <div class="col-md-10">
                            <div
                                class="dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-6 mb-md-0 mt-n6 mt-md-0">

                                <form action="{{ route('category-add-csv','start-target-group') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div id="DataTables_Table_0_filter" class="dataTables_filter">
                                        <label>
                                            <input type="file"  accept=".csv" required class="form-control" name="file" aria-controls="DataTables_Table_0">
                                        </label>
                                        <button class="btn btn-primary mx-2">Submit</button>
                                    </div>

                                </form>


                            </div>
                        </div>
                    </div>
                    <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                        <table class="table border-top dataTable" id="usersTable">
                            <thead class="table-light">
                                <tr>

                                    <th>Target group </th>
                                    <th>Action</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $category)
                                    <tr class="odd">
                                        <td class="">
                                            {{ $category->name }}
                                        </td>
                                        <td class="" style="">
                                            <div class="d-flex align-items-center">
                                                <a data-bs-toggle="modal" data-bs-target="#edit{{ $category->id }}"
                                                    class="text-body delete-record">
                                                    <i class="ti ti-edit x`ti-sm mx-2"></i>
                                                </a>

                                                <a data-bs-toggle="modal" data-bs-target="#deleteModal{{ $category->id }}"
                                                    class="text-body delete-record">
                                                    <i class="ti ti-trash x`ti-sm mx-2"></i>
                                                </a>
                                            </div>
                                            <div class="modal fade" id="deleteModal{{ $category->id }}" tabindex="-1"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                                                    <div class="modal-content deleteModal verifymodal">
                                                        <div class="modal-header">
                                                            <div class="modal-title" id="modalCenterTitle">Are you sure you
                                                                want to delete
                                                                this Target group ?
                                                            </div>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="body">After deleting the Title you will add a
                                                                new Target group </div>
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
                                                                        href="{{ route('category-delete', $category->id) }}">Delete</a>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal fade" id="edit{{ $category->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="modalCenterTitle">Target group 
                                                            </h5>
                                                        </div>
                                                        <form action="{{ route('category-edit' , $category->id) }}" id="addBusForm"
                                                            method="POST">
                                                            @csrf
                                                            
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col mb-3">
                                                                        <label for="nameWithTitle"
                                                                            class="form-label">Target group </label>
                                                                        <input type="text" id="nameWithTitle"
                                                                            name="name" value="{{ $category->name }}" class="form-control"
                                                                            placeholder="Target group " required />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-label-secondary"
                                                                    id="closeButton" data-bs-dismiss="modal">
                                                                    Close
                                                                </button>
                                                                <button type="submit" class="btn btn-primary">Edit
                                                                    Target group </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal fade" id="addNewBus" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalCenterTitle">Add New Target group </h5>
                            </div>
                            <form action="{{ route('category-add') }}" id="addBusForm" method="POST">
                                @csrf
                                <input type="hidden" name="type" id="" value="start-target-group">

                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="nameWithTitle" class="form-label">Target group </label>
                                            <input type="text" id="nameWithTitle" name="name" class="form-control"
                                                placeholder="Target group " required />
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-label-secondary" id="closeButton"
                                        data-bs-dismiss="modal">
                                        Close
                                    </button>
                                    <button type="submit" class="btn btn-primary">Add Target group </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection
    @section('script')
        <script>
            $(document).ready(function() {
                $('#closeButton').on('click', function(e) {
                    $('#addBusForm')[0].reset();
                });

            });
        </script>
    @endsection