@php
    $type = Session::get('app_type');
    $app_type = 'main';
    if ($type) {
        if ($type == 'affiliate') {
            $app_type = $type;
        }
    }
    if ($app_type == 'main') {
        $androidN = App\Models\AppSetting::where('name', 'android-new-version')->first();
        $androidO = App\Models\AppSetting::where('name', 'android-old-version')->first();
        $androidM = App\Models\AppSetting::where('name', 'android-version-message')->first();
    } else {
        $androidN = App\Models\AppSetting::where('type', 'affiliate')->where('name', 'android-new-version')->first();
        $androidO = App\Models\AppSetting::where('type', 'affiliate')->where('name', 'android-old-version')->first();
        $androidM = App\Models\AppSetting::where('type', 'affiliate')
            ->where('name', 'android-version-message')
            ->first();
    }
@endphp
@extends('layouts1.base')
@section('title', 'Android Version')
@section('main', 'Android Version Management')
@section('link')
    <link rel="stylesheet" href="assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">
            <!-- Users List Table -->
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <div class="col-2">
                            <select id="defaultSelect" class="form-select form-control" name="status" required="">
                                <option value="android" {{ $status == 'android' ? 'selected' : '' }}>Android
                                </option>
                                <option value="iOS" {{ $status == 'iOS' ? 'selected' : '' }}>iOS
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="frame__container py-3 mb-5">
                    <div class="row px-4">
                        <h5 class="text-center">Android Version</h5>
                        <form action="{{ route('dashboard-version-save', 'android') }}" id="addForm" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body pt-0">

                                <div class="row mb-2">
                                    <div class="col">
                                        <label for="" class="form-label">Android Old Version</label>
                                        <input type="text" required id="old-ver" class="form-control"
                                            name="android-old-version" placeholder="Enter old version"
                                            value="{{ $androidO->value ?? '' }}" />
                                    </div>

                                </div>
                                <div class="row mb-2">
                                    <div class="col">
                                        <label for="" class="form-label">Android New Version</label>
                                        <input type="text" required id="new-ver" class="form-control"
                                            name="android-new-version" placeholder="Enter new version"
                                            value="{{ $androidN->value ?? '' }}" />


                                    </div>

                                </div>
                                <div class="row mb-3">
                                    <div class="col">
                                        <label for="" class="form-label">Update Message</label>
                                        <input type="text" required id="new-ver" class="form-control"
                                            name="android-version-message" placeholder="Enter update message"
                                            value="{{ $androidM->value ?? '' }}" />
                                    </div>

                                </div>



                                <div class="row">
                                    <div class="col">
                                        {{-- <button type="submit" class="btn btn-primary saveBtn">Save Category</button> --}}
                                        <button type="submit" value="Submit" class="btn btn-primary saveBtn"
                                            id="signinButton" onclick="showLoader()">
                                            <span id="btntext" style="display: block">Save</span>


                                            <span class="align-middle" id="loader" role="status" style="display: none;">
                                                <span class="spinner-border" style="color: #ffffff" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </span>
                                            </span>
                                            <span class="execution-status" id="executionStatus"
                                                style="display: none;">0%</span>
                                        </button>
                                    </div>

                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>


        @endsection
        @section('script')

        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"
            integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous">
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"
            integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous">
        </script>
        <script>
            $("#defaultSelect").on("change", function() {
                var selectedValue = $(this).val(); // Get the selected value
                window.location.href = "/dashboard/version/" + selectedValue;
            });
        </script>

  
    @endsection