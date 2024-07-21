@extends('layouts.index')

@section('content')
    <body class="vertical-layout vertical-menu-modern  navbar-floating footer-static   menu-collapsed" data-open="click" data-menu="vertical-menu-modern" data-col="">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
    <!-- Include DataTables JS -->
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js" defer></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js" defer></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- BEGIN: Header-->
    @include('layouts.navbar')
    @include('layouts.sidebar')


    <!-- BEGIN: Content-->
    <div class="app-content content ">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper container-xxl p-0">
            <div class="content-header row">
                <div class="content-header-left col-md-9 col-12 mb-2">
                    <div class="row breadcrumbs-top">
                        <div class="col-12">
                            <h2 class="content-header-title float-left mb-0"><Users></Users></h2>
                            <div class="breadcrumb-wrapper">
                                <ol class="breadcrumb">

                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="content-body">
                <!-- Responsive Datatable -->
                <section id="ajax-datatable">
                    <div class="row">
                        <div class="col-12">


                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                                <div class="modal fade" id="cerImagesModal" tabindex="-1" role="dialog" aria-labelledby="cerImagesModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="cerImagesModalLabel">Cer Images</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="image-container">
                                                    <!-- Images will be displayed here based on user type -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <div class="card">
                                <div class="card-header border-bottom">
                                    <h4 class="card-title">Users Table</h4>
                                </div>
                                <div class="card-datatable">
                                    <table id="userstable" class="dt-responsive table">
                                        <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Address</th>
                                            <th>phone_number</th>
                                            <th>Type</th>
                                            <th> Cer Images</th>
                                            <th>Active</th>
                                            <th>delete</th>
                                        </tr>
                                        </thead>

                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!--/ Responsive Datatable -->

            </div>
        </div>
    </div>
    <script>
        $(document).ready(function () {
            $('#userstable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("get.users") }}',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'first_name', name: 'first_name'},
                    {data: 'email', name: 'email'},
                    {data: 'address', name: 'address'},
                    {data: 'phone_number', name: 'phone_number'},
                    {data: 'type', name: 'type'},
                    {data: 'cer_images', name: 'cer_images'},
                    {data: 'active', name: 'active'},
                    {data: 'delete', name: 'delete'},
                ]
            });

            // Event listener for the toggle switch change
            $(document).on('change', '.toggle-active', function () {
                var userId = $(this).data('id');
                var isActive = $(this).prop('checked');

                $.ajax({
                    url: "{{url('update-user-status')}}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        user_id: userId,
                        is_active: isActive
                    },
                    success: function (response) {
                        console.log(response);
                    },
                    error: function (xhr, textStatus, errorThrown) {
                        console.error('Error updating user status:', errorThrown);
                    }
                });
            });


            $(document).on('click', '.delete-user', function () {
                var userId = $(this).data('id');

                // Make an AJAX request to delete the user
                $.ajax({
                    url: "{{url('delete-user')}}", // Replace with your delete user route
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        user_id: userId,
                    },
                    success: function (response) {
                        console.log(response);
                        // Optionally, you can reload the DataTable after deleting the user
                        $('#userstable').DataTable().ajax.reload();
                    },
                    error: function (xhr, textStatus, errorThrown) {
                        console.error('Error deleting user:', errorThrown);
                    }
                });
            });

            // Show the modal when the "Add New" button is clicked
            $('.btn-success').click(function () {
                $('#addModal').modal('show');
            });

            $(document).on('click', '.view-cer-images', function () {
                var userId = $(this).data('id');

                // Make an AJAX request to get the user's type
                $.ajax({
                    url: "{{url('get-user-type')}}", // Replace with your route to get user type
                    method: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        user_id: userId
                    },
                    success: function (response) {
                        // Update the modal content based on the user type
                        updateModalContent(response.user.type, userId,response.user);
                        // Open the modal
                        $('#cerImagesModal').modal('show');
                    },
                    error: function (xhr, textStatus, errorThrown) {
                        console.error('Error getting user type:', errorThrown);
                    }
                });
            });

            function updateModalContent(userType, userId,user) {
                var imageContainer = $('.image-container');
                imageContainer.empty(); // Clear existing content

                // Add images based on user type
                if (userType === 'player') {
                    // Display profile_image
                    imageContainer.append('<img src="{{ asset('user_images') }}/' + user.profile_image + '" class="img-fluid" alt="Profile Image">');
                } else if (userType === 'coach') {
                    imageContainer.append('<img src="{{ asset('user_images') }}/' + user.id_image + '" class="img-fluid" alt="ID Image">');
                } else if (userType === 'store') {
                    imageContainer.append('<img src="{{ asset('user_images') }}/' + user.store_image + '" class="img-fluid" alt="Store Image">');
                    imageContainer.append('<img src="{{ asset('user_images') }}/' + user.tax_card_image + '" class="img-fluid" alt="Tax Card Image">');
                }
            }


        });



    </script>
    </body>

@endsection
