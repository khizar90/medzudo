<?php
$stop_app_message = App\Models\AppSetting::where('name', 'stop-app-message')->first()->value ?? '';
$stop_login_message = App\Models\AppSetting::where('name', 'stop-login-message')->first()->value ?? '';
$stop_signup_message = App\Models\AppSetting::where('name', 'stop-signup-message')->first()->value ?? '';
$stop_subscription_message = App\Models\AppSetting::where('name', 'stop-subscription-message')->first()->value ?? '';
$stop_post_message = App\Models\AppSetting::where('name', 'stop-post-message')->first()->value ?? '';
$stripe_message = App\Models\AppSetting::where('name', 'stripe-message')->first()->value ?? '';
$beta_code_message = App\Models\AppSetting::where('name', 'beta-code-message')->first()->value ?? '';

?>

@extends('layouts1.base')
@section('title', 'Emergency Check')
@section('main', 'Emergency Check Management')
@section('link')
    <link rel="stylesheet" href="/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
@endsection
@section('content')
    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">


            <div class="card">
                <div class="card-datatable table-responsive">
                    <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                        <table class="table border-top dataTable" id="usersTable">
                            <thead class="">
                                <tr>
                                    <th>Emergency</th>
                                    <th>Message</th>
                                    <th>Action</th>

                                </tr>
                            </thead>
                            <tbody>
                                <tr class="odd">
                                    <td>
                                        beta-code
                                    </td>
                                    <td>
                                        <form class="form-inline d-flex"
                                            action="{{ route('dashboard-emergency-message') }}" method="POST">
                                            @csrf
                                            <input type="text" hidden name="name" id=""
                                                value="beta-code-message">

                                            <textarea class="form-control" rows="2" placeholder="Enter value here" name="message" style="margin-right: 10px"
                                                required>{{ $beta_code_message }}</textarea>
                                            <button class="btn btn-outline-success " type="submit">Save</button>
                                        </form>

                                    </td>
                                    <td>
                                        <div class="col-sm-12">
                                            <label class="switch switch-primary">
                                                <input type="checkbox" class="switch-input"
                                                    {{ $beta_code == 1 ? 'checked' : '' }} name="beta-code" value="1"
                                                    onclick="check('beta-code')">
                                                <span class="switch-toggle-slider">
                                                </span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>

                                {{-- <tr class="odd">
                                    <td>
                                        is_firebase_query
                                    </td>
                                    <td>

                                    </td>
                                    <td>
                                        <div class="col-sm-12">
                                            <label class="switch switch-primary">
                                                <input type="checkbox" class="switch-input"
                                                    {{ $is_firebase_query == 1 ? 'checked' : '' }} name="is_firebase_query"
                                                    value="1" onclick="check('is_firebase_query')">
                                                <span class="switch-toggle-slider">
                                                </span>
                                            </label>
                                        </div>
                                    </td>
                                </tr> --}}
                                <tr class="odd">
                                    <td>
                                        stop-login
                                    </td>
                                    <td>
                                        <form class="form-inline d-flex"
                                            action="{{ route('dashboard-emergency-message') }}" method="POST">
                                            @csrf
                                            <input type="text" hidden name="name" id=""
                                                value="stop-login-message">

                                            <textarea class="form-control" rows="2" placeholder="Enter message here" name="message" style="margin-right: 10px"
                                                required>{{ $stop_login_message }}</textarea>
                                            <button class="btn btn-outline-success " type="submit">Save</button>
                                        </form>

                                    </td>
                                    <td>
                                        <div class="col-sm-12">
                                            <label class="switch switch-primary">
                                                <input type="checkbox" class="switch-input"
                                                    {{ $stop_login == 1 ? 'checked' : '' }} name="stop-login" value="1"
                                                    onclick="check('stop-login')">
                                                <span class="switch-toggle-slider">
                                                </span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                                <tr class="odd">
                                    <td>
                                        stop-signup
                                    </td>
                                    <td>
                                        <form class="form-inline d-flex"
                                            action="{{ route('dashboard-emergency-message') }}" method="POST">
                                            @csrf

                                            <input type="text" hidden name="name" id=""
                                                value="stop-signup-message">
                                            <textarea class="form-control" rows="2" name="message" placeholder="Enter message here" style="margin-right: 10px"
                                                required>{{ $stop_signup_message }}</textarea>
                                            <button class="btn btn-outline-success " type="submit">Save</button>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="col-sm-12">
                                            <label class="switch switch-primary">
                                                <input type="checkbox" class="switch-input"
                                                    {{ $stop_signup == 1 ? 'checked' : '' }} name="stop-signup"
                                                    value="1" onclick="check('stop-signup')">
                                                <span class="switch-toggle-slider">
                                                </span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                                {{-- <tr class="odd">
                                    <td>
                                        stop-subscription
                                    </td>
                                    <td>
                                        <form class="form-inline d-flex"
                                            action="{{ route('dashboard-emergency-message') }}" method="POST">
                                            @csrf
                                            <input type="text" hidden name="name" id=""
                                                value="stop-subscription-message">
                                            <textarea class="form-control" rows="2" placeholder="Enter message here" name="message" style="margin-right: 10px"
                                                required>{{ $stop_subscription_message }}</textarea>
                                            <button class="btn btn-outline-success " type="submit">Save</button>
                                        </form>

                                    </td>
                                    <td>
                                        <div class="col-sm-12">
                                            <label class="switch switch-primary">
                                                <input type="checkbox" class="switch-input"
                                                    {{ $stop_subscription == 1 ? 'checked' : '' }} name="stop-subscription"
                                                    value="1" onclick="check('stop-subscription')">
                                                <span class="switch-toggle-slider">
                                                </span>
                                            </label>
                                        </div>
                                    </td>
                                </tr> --}}

                                <tr class="odd">
                                    <td>
                                        stop-post
                                    </td>
                                    <td>
                                        <form class="form-inline d-flex"
                                            action="{{ route('dashboard-emergency-message') }}" method="POST">
                                            @csrf
                                            <input type="text" hidden name="name" id=""
                                                value="stop-post-message">

                                            <textarea class="form-control" rows="2" placeholder="Enter message here" name="message"
                                                style="margin-right: 10px" required>{{ $stop_post_message }}</textarea>
                                            <button class="btn btn-outline-success " type="submit">Save</button>
                                        </form>

                                    </td>
                                    <td>
                                        <div class="col-sm-12">
                                            <label class="switch switch-primary">
                                                <input type="checkbox" class="switch-input"
                                                    {{ $stop_post == 1 ? 'checked' : '' }} name="stop-post"
                                                    value="1" onclick="check('stop-post')">
                                                <span class="switch-toggle-slider">
                                                </span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>

                                <tr class="odd">
                                    <td>
                                        stop-app
                                    </td>
                                    <td>
                                        <form class="form-inline d-flex"
                                            action="{{ route('dashboard-emergency-message') }}" method="POST">
                                            @csrf
                                            <input type="text" hidden name="name" id=""
                                                value="stop-app-message">

                                            <textarea class="form-control" rows="2" name="message" placeholder="Enter message here"
                                                style="margin-right: 10px" required>{{ $stop_app_message }}</textarea>
                                            <button class="btn btn-outline-success " type="submit">Save</button>
                                        </form>


                                    </td>
                                    <td>
                                        <div class="col-sm-12">
                                            <label class="switch switch-primary">
                                                <input type="checkbox" class="switch-input"
                                                    {{ $stop_app == 1 ? 'checked' : '' }} name="stop-app" value="1"
                                                    onclick="check('stop-app')">
                                                <span class="switch-toggle-slider">
                                                </span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                                {{-- <tr class="odd">
                                    <td>
                                        stripe
                                    </td>
                                    <td>
                                        <form class="form-inline d-flex"
                                            action="{{ route('dashboard-emergency-message') }}" method="POST">
                                            @csrf
                                            <input type="text" hidden name="name" id=""
                                                value="stripe-message">

                                            <textarea class="form-control" rows="2" name="message" placeholder="Enter message here"
                                                style="margin-right: 10px" required>{{ $stripe_message }}</textarea>
                                            <button class="btn btn-outline-success " type="submit">Save</button>
                                        </form>


                                    </td>
                                    <td>
                                        <div class="col-sm-12">
                                            <label class="switch switch-primary">
                                                <input type="checkbox" class="switch-input"
                                                    {{ $stripe == 1 ? 'checked' : '' }} name="stripe" value="1"
                                                    onclick="check('stripe')">
                                                <span class="switch-toggle-slider">
                                                </span>
                                            </label>
                                        </div>
                                    </td>
                                </tr> --}}

                            </tbody>
                        </table>


                    </div>
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
            function check(type) {
                console.log(type);

                const checkbox = document.querySelector('input[name="' + type + '"]');

                checkbox.addEventListener('change', function() {
                    const isChecked = this.checked ? 1 : 0; // Convert boolean to 0 or 1
                    console.log(isChecked);
                    window.location.href = '/dashboard/emergency/check/' + type + '/' + isChecked;

                });

            }
        </script>
    @endsection
