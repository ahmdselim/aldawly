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
                            <div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form id="editCategoryForm" enctype="multipart/form-data">
                                                @csrf
                                                <div class="form-group">
                                                    <label for="editCategoryName">Category Name</label>
                                                    <input type="text" class="form-control" id="editCategoryName" name="name" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="editCategoryName">Category Name Ar</label>
                                                    <input type="text" class="form-control" id="editCategoryName-ar" name="name-ar" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="editCategoryImage">Category Image</label>
                                                    <input type="file" class="form-control" id="editCategoryImage" name="image">
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
                                    <h4 class="card-title">Categories Table</h4>
                                    <button type="button" class="btn btn-success add-category">Add Category</button>
                                </div>
                                <div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addCategoryModalLabel">Add Category</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form id="addCategoryForm" enctype="multipart/form-data"> <!-- Add enctype attribute -->
                                                    @csrf
                                                    <div class="form-group">
                                                        <label for="addCategoryName">Category Name EN</label>
                                                        <input type="text" class="form-control" id="addCategoryName" name="name" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="addCategoryName-ar">Category Name Ar</label> <!-- Correct ID for Arabic input -->
                                                        <input type="text" class="form-control" id="addCategoryName-ar" name="name_ar" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="addCategoryImage">Category Image</label> <!-- Image input field -->
                                                        <input type="file" class="form-control" id="addCategoryImage" name="image" accept="image/*" required>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary">Add Category</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-datatable">
                                    <table id="catstable" class="dt-responsive table">
                                        <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name_ar</th>
                                            <th>Name_en</th>
                                            <th>Image</th>
                                            <th>update</th>
                                            <th>delete</th>
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

            $('#catstable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("get.categories") }}',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name_ar', name: 'name'},
                    {data: 'name', name: 'name'},
                    {data: 'image', name: 'image'},
                    {data: 'edit', name: 'edit'},
                    {data: 'delete', name: 'delete'},
                ]
            });

            $(document).on('click', '.delete-category', function () {
                var categoryId = $(this).data('id');

                // Make an AJAX request to delete the category
                $.ajax({
                    url: "{{url('delete-category')}}", // Replace with your route to delete category
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        category_id: categoryId
                    },
                    success: function (response) {
                        console.log(response);
                        showSuccessMessage('Category deleted successfully!');
                        $('#catstable').DataTable().ajax.reload();
                    },
                    error: function (xhr, textStatus, errorThrown) {
                        console.error('Error deleting category:', errorThrown);
                        showErrorMessage('Error deleting category. Please try again.');
                    }
                });

            });

            $(document).on('click', '.edit-category', function () {
                var categoryId = $(this).data('id');

                // Make an AJAX request to get category details
                $.ajax({
                    url: "{{url('get-category-details')}}", // Replace with your route to get category details
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        category_id: categoryId
                    },
                    success: function (response) {
                        // Populate the modal form with old category data
                        $('#editCategoryName').val(response.category.name);
                        $('#editCategoryName-ar').val(response.category.name_ar);
                        // Set the category ID as a data attribute for the form
                        $('#editCategoryForm').data('id', categoryId);
                        // Open the modal for editing
                        $('#editCategoryModal').modal('show');
                    },
                    error: function (xhr, textStatus, errorThrown) {
                        console.error('Error getting category details:', errorThrown);
                    }
                });
            });

// Event listener for the category edit form submission
            $('#editCategoryForm').submit(function (e) {
                e.preventDefault();

                var categoryId = $(this).data('id');
                var categoryName = $('#editCategoryName').val();
                var categoryArName = $('#editCategoryName-ar').val();
                var formData = new FormData(this);
                formData.append('category_id', categoryId);
                formData.append('name', categoryName);
                formData.append('name_ar', categoryArName);


                $.ajax({
                    url: "{{ url('update-category') }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        console.log(response);
                        showSuccessMessage('Category updated successfully!');
                        // Optionally, you can reload the DataTable after updating the category
                        $('#catstable').DataTable().ajax.reload();
                        // Close the modal after updating
                        $('#editCategoryModal').modal('hide');
                    },
                    error: function (xhr, textStatus, errorThrown) {
                        console.error('Error updating category:', errorThrown);
                        showErrorMessage('Error updating category. Please try again.');
                    }
                });
            });
            $(document).on('click', '.add-category', function () {
                // Clear the form inputs
                $('#addCategoryForm')[0].reset();
                // Open the modal for adding a new category
                $('#addCategoryModal').modal('show');
            });

// Event listener for the add category form submission
            $('#addCategoryForm').submit(function (e) {
                e.preventDefault();

                // Get form data
                var formData = new FormData(this);

                // Make an AJAX request to add a new category
                $.ajax({
                    url: "{{ url('add-category') }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function (response) {
                        console.log(response);
                        showSuccessMessage('Category added successfully!');
                        $('#catstable').DataTable().ajax.reload();
                        $('#addCategoryModal').modal('hide');
                    },
                    error: function (xhr, textStatus, errorThrown) {
                        console.error('Error adding category:', errorThrown);
                        showErrorMessage('Error adding category. Please try again.');
                    }
                });
            });
        });
    </script>
    </body>

@endsection
