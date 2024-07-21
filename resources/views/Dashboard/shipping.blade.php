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
                            <div class="modal fade" id="editGovernmentModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editCategoryModalLabel"> Edit Government</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form id="editGovernmentForm">
                                                @csrf
                                                <div class="form-group">
                                                    <label for="editGovernmentName">Government name </label>
                                                    <input type="text" class="form-control" id="editGovernmentName" name="name" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="editGovernmentprice1">cash price </label>
                                                    <input type="text" class="form-control" id="editGovernmentprice1" name="cash_price" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="editGovernmentprice2">voda_cash price </label>
                                                    <input type="text" class="form-control" id="editGovernmentprice2" name="voda_price" required>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="alert alert-success" id="successMessage" style="display: none;"></div>
                                <div class="alert alert-danger" id="errorMessage" style="display: none;"></div>
                                <div class="card-header border-bottom">
                                    <h4 class="card-title">Governments Table</h4>
                                    <button type="button" class="btn btn-success add-government">Add Government</button>
                                </div>
                                <div class="modal fade" id="addGovernmentModal" tabindex="-1" role="dialog" aria-labelledby="addGovernmentModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addGovernmentModalLabel">Add Government</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form id="addGovernmentForm">
                                                    @csrf
                                                    <div class="form-group">
                                                        <label for="addGovernmentName">Government Name</label>
                                                        <input type="text" class="form-control" id="addGovernmentName" name="name" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="addGovernmentPrice1">Cash price</label>
                                                        <input type="text" class="form-control" id="addGovernmentprice1" name="cash_price" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="addGovernmentPrice2">voda Cash price </label>
                                                        <input type="text" class="form-control" id="addGovernmentprice2" name="voda_price" required>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Add Government</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-datatable">
                                    <table id="Govtable" class="dt-responsive table">
                                        <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>CashPrice</th>
                                            <th>VodaCashPrice</th>
                                            <th>Edit</th>
                                            <th>Delete</th>
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

        $(document).ready(function () {

            function showSuccessMessage(message) {
                $('#successMessage').text(message).show();
                setTimeout(function () {
                    $('#successMessage').fadeOut('slow');
                }, 5000); // Hide after 5 seconds
            }

            // Function to show error message
            function showErrorMessage(message) {
                $('#errorMessage').text(message).show();
                setTimeout(function () {
                    $('#errorMessage').fadeOut('slow');
                }, 5000); // Hide after 5 seconds
            }

            $('#Govtable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("government.show") }}',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                },
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'cash_price', name: 'cash_price' },
                    { data: 'voda_price', name: 'voda_price' },
                    { data: 'edit', name: 'edit' },
                    { data: 'delete', name: 'delete' },
                ]
            });

            $(document).on('click', '.add-government', function () {
                // Clear the form inputs
                $('#addGovernmentForm')[0].reset();
                // Open the modal for adding a new category
                $('#addGovernmentModal').modal('show');
            });

            $('#addGovernmentForm').submit(function (e) {
                e.preventDefault();

                var formData = $(this).serialize(); // Serialize form data

                $.ajax({
                    url: '{{ url('add/government') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: formData, // Use serialized form data
                    success: function (response) {
                        console.log(response);
                        showSuccessMessage('Government added successfully!');
                        $('#Govtable').DataTable().ajax.reload();
                        $('#addGovernmentModal').modal('hide');
                    },
                    error: function (xhr, textStatus, errorThrown) {
                        console.error('Error adding government:', errorThrown);
                        showErrorMessage('Error adding government. Please try again.');
                    }
                });
            });


            $(document).on('click', '.edit-government', function () {
                var governmentId = $(this).data('id');
                $.ajax({
                    url: '{{ url('government') }}/' + governmentId,
                    method: 'GET',
                    success: function (response) {
                        $('#editGovernmentForm #editGovernmentName').val(response.government.name);
                        $('#editGovernmentForm #editGovernmentprice1').val(response.government.cash_price);
                        $('#editGovernmentForm #editGovernmentprice2').val(response.government.voda_price);
                        $('#editGovernmentForm').attr('data-id', governmentId);
                        $('#editGovernmentModal').modal('show');
                    },
                    error: function (xhr, textStatus, errorThrown) {
                        console.error('Error fetching government data:', errorThrown);
                        // Handle error
                    }
                });
            });

            $('#editGovernmentForm').submit(function (e) {
                e.preventDefault();
                var formData = $(this).serialize();
                var governmentId = $(this).attr('data-id'); // Get the ID from the form attribute

                $.ajax({
                    url: '{{ url('update/government') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: formData + '&id=' + governmentId,
                    success: function (response) {
                        console.log(response);
                        showSuccessMessage('Government updated successfully!');
                        $('#Govtable').DataTable().ajax.reload();
                        $('#editGovernmentModal').modal('hide');
                    },
                    error: function (xhr, textStatus, errorThrown) {
                        console.error('Error updating government:', errorThrown);
                        showErrorMessage('Error updating government. Please try again.');
                    }
                });
            });


            $(document).on('click', '.delete-government', function () {
                var governmentId = $(this).data('id');

                    $.ajax({
                        url: '{{ url('government/delete') }}/' + governmentId,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            console.log(response);
                            showSuccessMessage('Government deleted successfully!');
                            $('#Govtable').DataTable().ajax.reload();
                        },
                        error: function (xhr, textStatus, errorThrown) {
                            console.error('Error deleting government:', errorThrown);
                            showErrorMessage('Error deleting government. Please try again.');
                        }
                    });

            });
        });
    </script>
    </body>

@endsection
