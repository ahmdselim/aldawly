@extends('layouts.index')

@section('content')
    <body class="vertical-layout vertical-menu-modern navbar-floating footer-static menu-collapsed" data-open="click" data-menu="vertical-menu-modern" data-col="">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <!-- Include DataTables CSS -->
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
                            <!-- Add Slider Modal -->
                            <div class="modal fade" id="addSliderModal" tabindex="-1" role="dialog" aria-labelledby="addSliderModalLabel" aria-hidden="true">
                                <!-- Add Slider Form -->
                                <form id="addSliderForm" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addSliderModalLabel">Add Slider</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="sliderImage">Slider Image</label>
                                                    <input type="file" class="form-control" id="sliderImage" name="image" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="sliderUrl">Slider URL</label>
                                                    <input type="text" class="form-control" id="sliderUrl" name="url" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Add Slider</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="modal fade" id="editSliderModal" tabindex="-1" role="dialog" aria-labelledby="editSliderModalLabel" aria-hidden="true">
                                <form id="editSliderForm" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editSliderModalLabel">Edit Slider</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label for="editSliderUrl">Slider URL</label>
                                                    <input type="text" class="form-control" id="editSliderUrl" name="url" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="editSliderImage">New Slider Image</label>
                                                    <input type="file" class="form-control" id="editSliderImage" name="image">
                                                </div>
                                                <div class="form-group">
                                                    <label for="editSliderImagePreview">Current Slider Image</label>
                                                    <img id="editSliderImagePreview" src="" alt="Slider Image Preview" class="img-fluid">
                                                </div>

                                                <input hidden type="text" id="slider_id" name="slider_id">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary">Update Slider</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="card">
                                <!-- Alert Messages -->
                                <div class="alert alert-success" id="successMessage" style="display: none;"></div>
                                <div class="alert alert-danger" id="errorMessage" style="display: none;"></div>
                                <div class="card-header border-bottom">
                                    <h4 class="card-title">Sliders Table</h4>
                                    <button type="button" class="btn btn-success add-slider" data-toggle="modal" data-target="#addSliderModal">Add Slider</button>
                                </div>
                                <div class="card-datatable">
                                    <!-- Sliders Table -->
                                    <table id="slidersTable" class="dt-responsive table">
                                        <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Image</th>
                                            <th>URL</th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // DataTable Initialization
            $('#slidersTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("sliders.get") }}',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'image', name: 'image' },
                    { data: 'url', name: 'url' },
                    { data: 'actions', name: 'actions' },
                ]
            });

            // Add Slider Form Submission
            $('#addSliderForm').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajax({
                    url: '{{ route("sliders.add") }}',
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#addSliderModal').modal('hide');
                        $('#successMessage').text('Slider added successfully!').show();
                        $('#slidersTable').DataTable().ajax.reload();
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        console.error('Error adding slider:', errorThrown);
                        $('#errorMessage').text('Error adding slider. Please try again.').show();
                    }
                });
            });

            $(document).on('click', '.edit-slider', function () {
                var sliderId = $(this).data('id');

                // Make AJAX request to get slider details
                $.ajax({
                    url: '{{ route("sliders.details") }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        slider_id: sliderId
                    },
                    success: function (response) {
                        $('#editSliderUrl').val(response.slider.url);
                        $('#slider_id').val(sliderId);

                        // Set the image preview
                        var imageUrl = response.slider.image;
                        $('#editSliderImagePreview').attr('src', 'sliders/'+imageUrl);

                        // Set the slider ID as a data attribute for the form
                        $('#editSliderForm').attr('data-id', sliderId);

                        // Open the edit modal
                        $('#editSliderModal').modal('show');
                    },
                    error: function (xhr, textStatus, errorThrown) {
                        console.error('Error getting slider details:', errorThrown);
                        // Handle error if necessary
                    }
                });
            });

            $('#editSliderForm').submit(function(e) {
                e.preventDefault();
                var formData = new FormData(this);
                var sliderId = $('#slider_id').val();

                formData.append('slider_id', sliderId);

                $.ajax({
                    url: '{{ route("sliders.update") }}',
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#editSliderModal').modal('hide');
                        $('#successMessage').text('Slider updated successfully!').show();
                        $('#slidersTable').DataTable().ajax.reload();
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        console.error('Error updating slider:', errorThrown);
                        $('#errorMessage').text('Error updating slider. Please try again.').show();
                    }
                });
            });
            $(document).on('click', '.delete-slider', function () {
                var sliderId = $(this).data('id');

                // Confirm deletion

                    $.ajax({
                        url: '{{ route("sliders.delete") }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            slider_id: sliderId
                        },
                        success: function (response) {
                            $('#successMessage').text('Slider deleted successfully!').show();
                            $('#slidersTable').DataTable().ajax.reload();
                        },
                        error: function (xhr, textStatus, errorThrown) {
                            console.error('Error deleting slider:', errorThrown);
                            $('#errorMessage').text('Error deleting slider. Please try again.').show();
                        }
                    });

            });
        });
    </script>
    </body>
@endsection
