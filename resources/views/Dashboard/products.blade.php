@extends('layouts.index')
@section('content')
    <body class="vertical-layout vertical-menu-modern  navbar-floating footer-static   menu-collapsed" data-open="click" data-menu="vertical-menu-modern" data-col="">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
    <!-- Include DataTables JS -->
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js" defer></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js" defer></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>
    <link href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css" rel="stylesheet" />    <!-- BEGIN: Header-->
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
                <div class="modal fade" id="sizesModal" tabindex="-1" role="dialog" aria-labelledby="sizesModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="sizesModalLabel">Sizes & Prices</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div id="sizesContent">
                                    <!-- Sizes and prices content will appear here dynamically -->
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addProductModalLabel">Add Product</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="addProductForm">

                                    <div class="form-group">
                                        <label for="productName">Product Name</label>
                                        <input type="text" class="form-control" name="productName" id="productName" placeholder="Product Name">
                                    </div>
                                    <div class="form-group">
                                        <label for="productName">Product Name AR</label>
                                        <input type="text" class="form-control" name="productName_ar" id="productName_ar" placeholder="Product Name_ar">
                                    </div>
                                    <div class="form-group">
                                        <label for="productDescription">Product Description</label>
                                        <input type="text" class="form-control" name="productDescription" id="productDescription" placeholder="Product Description">
                                    </div>
                                    <div class="form-group">
                                        <label for="productDescription">Product Description Ar</label>
                                        <input type="text" class="form-control" name="productDescription_ar" id="productDescription_ar" placeholder="Product Description_ar">
                                    </div>
                                    <div class="form-group">
                                        <label for="SubCategory">SubCategory</label>
                                        <select class="form-control" id="subcat" name="subcat">
                                            <option value="men">Men</option>
                                            <option value="women">Women</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="image">Product Images</label>
                                        <input type="file" class="form-control-file" name="images[]" multiple id="images">
                                    </div>
                                    <div class="form-group">
                                        <label for="categories">Category</label>
                                        @php $categories = \App\Models\category::get() @endphp
                                        <select class="form-control" id="cat_id" name="cat_id">
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group" id="sizesGroup">
                                        <div id="sizeInputs">
                                            <div class="size-input-group" data-group-index="0">
                                            <div class="form-group" id="sizesGroup">
                                                <label for="sizes">Sizes & Prices</label>
                                                <div class="input-group mb-2">
                                                    <input type="text" class="form-control" name="sizes[]" placeholder="Size">
                                                    <input type="number" class="form-control" name="coach_prices[]" placeholder="Coach Price">
                                                    <input type="number" class="form-control" name="store_prices[]" placeholder="Store Price">
                                                    <input type="number" class="form-control" name="player_prices[]" placeholder="Player Price">
                                                    <select class="form-control select2" name="colors[]" multiple="multiple" data-placeholder="Select Colors">
                                                        <option value="0xffff0000">Red</option>
                                                        <option value="0xffffa500">Orange</option>
                                                        <option value="0xfffff000">Yellow</option>
                                                        <option value="0xff00ff00">Green</option>
                                                        <option value="0xff0000ff">Blue</option>
                                                        <option value="0xff800080">Purple</option>
                                                        <option value="0xffffc0cb">Pink</option>
                                                        <option value="0xffffffff">White</option>
                                                        <option value="0xff000000">Black</option>
                                                        <option value="0xff808080">Gray</option>
                                                        <option value="0xffffd700">Gold</option>
                                                        <option value="0xffa52a2a">Brown</option>
                                                        <option value="0xffccae94">cashmere</option>
                                                        <option value="0xff66ccff">sky-blue</option>
                                                        <option value="0xffcc33ff">Purple</option>
                                                        <option value="0xffc2c2d6">silver</option>
                                                    </select>
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary add-size" type="button">+</button>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" id="saveProduct">Save Product</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="updatePricesModal" tabindex="-1" role="dialog" aria-labelledby="updatePricesModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="updatePricesModalLabel">Update Prices</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="updatePricesForm">
                                    <input type="text" hidden class="form-control" name="product_id_prices" id="product_id_prices" placeholder="product_id_prices">
                                    <div class="form-group">
                                        <label for="sizes_prices">Sizes & Prices</label>
                                        <div class="input-group mb-2" id="sizesGroup_prices">
                                            <!-- Sizes and prices content will appear here dynamically -->
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" id="updatePrices">Update Prices</button>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="modal fade" id="updateProductModal" tabindex="-1" role="dialog" aria-labelledby="updateProductModal" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addProductModalLabel">Update Product</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="updateProductForm">
                                    <input type="text" hidden class="form-control" name="product_idu" id="product_idu" placeholder="product_idu">
                                    <div class="form-group">
                                        <label for="productName">Product Name</label>
                                        <input type="text" class="form-control" name="productNameu" id="productNameu" placeholder="Product Name">
                                    </div>
                                    <div class="form-group">
                                        <label for="productName">Product Name AR</label>
                                        <input type="text" class="form-control" name="productName_aru" id="productName_aru" placeholder="Product Name">
                                    </div>
                                    <div class="form-group">
                                        <label for="productDescription">Product Description</label>
                                        <input type="text" class="form-control" name="productDescriptionu" id="productDescriptionu" placeholder="Product Description">
                                    </div>
                                    <div class="form-group">
                                        <label for="productDescription">Product Description AR</label>
                                        <input type="text" class="form-control" name="productDescription_aru" id="productDescription_aru" placeholder="Product Description">
                                    </div>
{{--                                    <div class="form-group">--}}
{{--                                        <label for="color">Color</label>--}}
{{--                                        <select class="form-control select2" name="coloru" id="coloru">--}}
{{--                                            <option value="">Select a color</option>--}}
{{--                                            <option value="#FF0000">Red</option>--}}
{{--                                            <option value="#FFA500">Orange</option>--}}
{{--                                            <option value="#FFFF00">Yellow</option>--}}
{{--                                            <option value="#00FF00">Green</option>--}}
{{--                                            <option value="#0000FF">Blue</option>--}}
{{--                                            <option value="#800080">Purple</option>--}}
{{--                                            <option value="#FFC0CB">Pink</option>--}}
{{--                                            <option value="#FFFFFF">White</option>--}}
{{--                                            <option value="#000000">Black</option>--}}
{{--                                            <option value="#808080">Gray</option>--}}
{{--                                            <option value="#FFD700">Gold</option>--}}
{{--                                            <option value="#A52A2A">Brown</option>--}}
{{--                                        </select>--}}
{{--                                    </div>--}}
                                    <div class="form-group">
                                        <label for="SubCategory">SubCategory</label>
                                        <select class="form-control" id="subcatu" name="subcatu">
                                            <option value="men">Men</option>
                                            <option value="women">Women</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="image">Product Image</label>
                                        <input type="file" class="form-control-file" multiple name="imageu[]" id="imageu[]">
                                        <img id="productImageu" src="" alt="Product Image" width="50" height="50">
                                    </div>
                                    <div class="form-group">
                                        <label for="categories">Category</label>
                                        @php $catrgories = \App\Models\category::get() @endphp
                                        <select class="form-control" id="cat_idu" name="cat_idu">
                                            @foreach($catrgories as $category)
                                                <option value="{{$category->id}}" >{{$category->name}}</option>
                                            @endforeach
                                        </select>

                                    </div>
                                    <div class="form-group" id="sizesGroupu">
                                        <label for="sizes">Sizes & Prices</label>

                                    </div>

                                    <div class="form-group" id="colors_stock">
                                        <label for="colors">colors</label>

                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary" id="UpdateProduct">Update Product</button>
                            </div>
                        </div>
                    </div>
                </div>

                <section id="ajax-datatable">
                    @if(Session::has('success'))
                        <div class="alert alert-success">
                            {{ Session::get('success') }}
                        </div>
                    @endif

                    @if(Session::has('error'))
                        <div class="alert alert-danger">
                            {{ Session::get('error') }}
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="alert alert-success" id="successMessage" style="display: none;"></div>
                                <div class="alert alert-danger" id="errorMessage" style="display: none;"></div>
                                <div class="card-header border-bottom">
                                    <h4 class="card-title">Products Table</h4>
                                    <button type="button" class="btn btn-success add-product">Add Product</button>
                                </div>
                                <div class="card-datatable">
                                    <table id="productstable" class="dt-responsive table-responsive table">
                                        <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Name_AR</th>
                                            <th>Description</th>
                                            <th>Description_AR</th>
{{--                                            <th>Image</th>--}}
                                            <th>Category</th>
                                            <th>sizes&prices</th>
                                            <th>Add offer</th>
                                            <th>is_offer</th>
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

            $('#productstable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("get.products") }}',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'Name', name: 'Name'},
                    {data: 'Name_ar', name: 'Name'},
                    {data: 'Description', name: 'Description'},
                    {data: 'Description_ar', name: 'Description'},
                    // {data: 'productImage', name: 'productImage'},
                    {data: 'CategoryName', name: 'CategoryName'},
                    {data: 'sizes', name: 'sizes'},
                    {data: 'add_offer', name: 'add_offer'},
                    {data: 'is_offer', name: 'is_offer'},
                    {data: 'edit', name: 'edit'},
                    {data: 'delete', name: 'delete'},
                ]
            });
            $('.add-product').click(function () {
                $('#addProductModal').modal('show');
            });
            let sizeCounter = 0;

            function addNewSizeInput() {
                const lastGroupIndex = $('.size-input-group').last().data('group-index') || 0;
                const newGroupIndex = lastGroupIndex + 1;

                const newSizeInputGroup = `
        <div class="size-input-group" data-group-index="${newGroupIndex}" id="sizeInput${newGroupIndex}">
            <div class="form-group">
                <label for="sizes">Sizes & Prices</label>
                <div class="input-group mb-2">
                    <input type="text" class="form-control" name="sizes[]" placeholder="Size">
                    <input type="number" class="form-control" name="coach_prices[]" placeholder="Coach Price">
                    <input type="number" class="form-control" name="store_prices[]" placeholder="Store Price">
                    <input type="number" class="form-control" name="player_prices[]" placeholder="Player Price">
                    <select class="form-control select2" name="colors[]" multiple="multiple" data-placeholder="Select Colors">
                     <option value="0xffff0000">Red</option>
<option value="0xffffa500">Orange</option>
<option value="0xfffff000">Yellow</option>
<option value="0xff00ff00">Green</option>
<option value="0xff0000ff">Blue</option>
<option value="0xff800080">Purple</option>
<option value="0xffffc0cb">Pink</option>
<option value="0xffffffff">White</option>
<option value="0xff000000">Black</option>
<option value="0xff808080">Gray</option>
<option value="0xffffd700">Gold</option>
<option value="0xffa52a2a">Brown</option>
<option value="0xffccae94">cashmere</option>
<option value="0xff66ccff">sky-blue</option>
<option value="0xffcc33ff">Purple</option>
<option value="0xffc2c2d6">silver</option>
                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary add-size" type="button">+</button>
                        <button class="btn btn-outline-secondary remove-size" type="button" data-id="${newGroupIndex}">-</button>
                    </div>
                </div>
            </div>
        </div>
    `;
                $('#sizeInputs').append(newSizeInputGroup);

                // Initialize Select2 for the newly added select element
                $('.select2').select2();
                sizeCounter++;
            }

// Add new size input on plus button click
            $('body').on('click', '.add-size', function () {
                addNewSizeInput();
            });

// Remove size input on minus button click
            $('body').on('click', '.remove-size', function () {
                const sizeId = $(this).data('id');
                $(`#sizeInput${sizeId}`).remove();
            });
            $('.select2').select2();

            $('#saveProduct').click(function (e) {
                e.preventDefault(); // Prevent default form submission

                const formData = new FormData($('#addProductForm')[0]); // Create FormData object from the form

                const sizesArray = [];

// Loop through each size input
                $('[name="sizes[]"]').each(function (index, sizeInput) {
                    const size = $(sizeInput).val(); // Get the size value

                    // Get the index of the group
                    const groupIndex = $(sizeInput).closest('.size-input-group').data('group-index');

                    // Find the select2 element within the same parent group
                    const select2Element = $(`[data-group-index="${groupIndex}"]`).find('.select2');

                    // Get the selected colors from the select2 element
                    const colors = select2Element.val();

                    console.log('Size:', size);
                    console.log('Colors:', colors); // Log colors to ensure they are collected correctly

                    // Push size and colors to sizesArray with index
                    sizesArray.push({size: size, colors: colors ? colors.map(color => color) : []});
                });

// Append sizesArray to formData
                formData.append('sizes', JSON.stringify(sizesArray));


                $.ajax({
                    url: '{{ route("add.product") }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success) {
                            showSuccessMessage(response.success);
                            $('#addProductModal').modal('hide');
                            $('#productstable').DataTable().ajax.reload(); // Refresh DataTable
                        } else {
                            showErrorMessage('Unexpected response received.');
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 400 && xhr.responseJSON && xhr.responseJSON.error) {
                            let errors = xhr.responseJSON.error;
                            let errorMessage = 'Validation Error:';
                            for (let key in errors) {
                                if (errors.hasOwnProperty(key)) {
                                    errorMessage += `${errors[key][0]}`;
                                }
                            }
                            showErrorMessage(errorMessage);
                            $('#addProductModal').modal('hide');
                        } else {
                            showErrorMessage('An error occurred while processing the request.');
                        }
                    }
                });
            });
            $(document).on('click', '.sizes-prices', function () {
                let productId = $(this).data('id');
                const colorMapping = {
                    '0xffff0000': 'Red',
                    '0xffffa500': 'Orange',
                    '0xfffff000': 'Yellow',
                    '0xff00ff00': 'Green',
                    '0xff0000ff': 'Blue',
                    '0xff800080': 'Purple',
                    '0xffffc0cb': 'Pink',
                    '0xffffffff': 'White',
                    '0xff000000': 'Black',
                    '0xff808080': 'Gray',
                    '0xffffd700': 'Gold',
                    '0xffa52a2a': 'Brown',
                    '0xffccae94': 'cashmere',
                    '0xff66ccff': 'sky-blue',
                    '0xffcc33ff': 'purple',
                    '0xffc2c2d6': 'silver',
                };
                function getColorName(color) {
                    return colorMapping[color.color_name];
                }
                $.ajax({
                    url: '{{ url('get-sizes-prices') }}/' + productId,
                    method: 'GET',
                    success: function (response) {
                        if (response.success) {
                            let sizesAndPrices = response.data;

                            let modalContent = '';
                            sizesAndPrices.forEach(function (sizeAndPrice) {
                                modalContent += `<p>Size: ${sizeAndPrice.size}</p>`;
                                modalContent += `<p>Coach Price: ${sizeAndPrice.Coach_price}</p>`;
                                modalContent += `<p>Store Price: ${sizeAndPrice.Store_price}</p>`;
                                modalContent += `<p>Player Price: ${sizeAndPrice.Player_price}</p>`;

                                // Add colors
                                modalContent += '<p>Colors:</p>';
                                if (sizeAndPrice.colors.length > 0) {
                                    modalContent += '<ul>';
                                    sizeAndPrice.colors.forEach(function (color) {
                                        const colorName = getColorName(color);
                                        modalContent += `<li>${colorName}</li>`;
                                    });
                                    modalContent += '</ul>';
                                } else {
                                    modalContent += '<p>No colors available</p>';
                                }

                                modalContent += `<hr>`;
                            });

                            $('#sizesContent').html(modalContent);
                            $('#sizesModal').modal('show');
                        } else {
                            // Handle error or no data scenario
                        }
                    },
                    error: function (error) {
                        // Handle error scenario
                    }
                });
            });

            $(document).ready(function () {


                $('#updatePrices').on('click', function () {
                    var productId = $('#product_id_prices').val();
                    var sizes_prices = [];

                    // Loop through each size input field and gather data
                    $('.offer').each(function () {
                        var size = $(this).find('input[name="sizes_prices[]"]').val();
                        var size_id = $(this).find('input[name="size_id"]').val();

                        sizes_prices.push({
                            size: size,
                            size_id: size_id,  // Include the size_id in the data
                            Coach_price: $(this).find('input[name="coach_prices_prices[]"]').val(),
                            Store_price: $(this).find('input[name="store_prices_prices[]"]').val(),
                            Player_price: $(this).find('input[name="player_prices_prices[]"]').val(),
                        });
                    });

                    // Send the data to the server
                    $.ajax({
                        url: '{{ url('update_prices') }}/' + productId,
                        method: 'POST',
                        data: {
                            sizes_prices: sizes_prices,
                            product_id: productId,
                            size_ids: sizes_prices.map(item => item.size_id),  // Include size_ids array
                            // You can add more data if needed
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (response) {
                            if (response.success) {
                                $('#updatePricesModal').modal('hide');
                            } else {
                                // Handle error scenario
                                console.error(response.error);
                            }
                        },
                        error: function (error) {
                            // Handle error scenario
                            console.error(error);
                        }
                    });
                });

                $(document).on('click', '.add-offer', function () {
                    var productId = $(this).data('id');

                    $.ajax({
                        url: '{{ url('get_sizes_with_prices') }}/' + productId,
                        method: 'GET',
                        success: function (response) {
                            if (response) {
                                var prices = response; // Array of objects containing sizes and all three prices

                                $('#sizesGroup_prices').empty();
                                prices.forEach(function (price) {
                                    var sizeInput = '<div class="input-group  offer mb-2">';
                                    sizeInput += '<input type="text" class="form-control" readonly name="sizes_prices[]" placeholder="Size" value="' + price.size + '">';
                                    sizeInput += '<input type="text" class="form-control" hidden readonly name="size_id"  value="' + price.id + '">';
                                    sizeInput += '<input type="number" class="form-control" name="coach_prices_prices[]" placeholder="Coach Price" value="' + price.Coach_price + '">';
                                    sizeInput += '<input type="number" class="form-control" name="store_prices_prices[]" placeholder="Store Price" value="' + price.Store_price + '">';
                                    sizeInput += '<input type="number" class="form-control" name="player_prices_prices[]" placeholder="Player Price" value="' + price.Player_price + '">';
                                    sizeInput += '</div>';
                                    $('#sizesGroup_prices').append(sizeInput);
                                });

                                $('#product_id_prices').val(productId);
                                $('#updatePricesModal').modal('show');
                            } else {
                                // Handle error or no data scenario
                            }
                        },
                        error: function (error) {
                            // Handle error scenario
                        }
                    });
                });

                $('body').on('click', '.edit-product', function () {

                    var productId = $(this).data('id');
                    $('#product_idu').val(productId);
                    $.ajax({
                        url: '{{ url('get_product_details') }}/' + productId,
                        method: 'GET',
                        success: function (response) {
                            console.log(response)
                            $('#productNameu').val(response.productName);
                            $('#productName_aru').val(response.name_ar);
                            $('#productDescriptionu').val(response.productDescription);
                            $('#productDescription_aru').val(response.description_ar);
                            $('#coloru').val(response.color);
                            $('#subcatu').val(response.subcat);
                            $('#cat_idu').val(response.cat_id);


                            var imageURL = response.productImage;

                            // Set the image source in the modal
                            $('#productImageu').attr('src', '/products/' + imageURL);
                            // Fetch sizes with prices for the product
                        },
                    });
                    var colorMap = {
                        '0xffff0000': 'Red',
                        '0xffffa500': 'Orange',
                        '0xfffff000': 'Yellow',
                        '0xff00ff00': 'Green',
                        '0xff0000ff': 'Blue',
                        '0xff800080': 'Purple',
                        '0xffffc0cb': 'Pink',
                        '0xffffffff': 'White',
                        '0xff000000': 'Black',
                        '0xff808080': 'Gray',
                        '0xffffd700': 'Gold',
                        '0xffa52a2a': 'Brown',
                        '0xffccae94': 'cashmere',
                        '0xff66ccff': 'sky-blue',
                        '0xffcc33ff': 'purple',
                        '0xffc2c2d6': 'silver',
                        // Add more colors as needed
                    };

                    $.ajax({
                        url: '{{ url('get_sizes_with_prices') }}/' + productId,
                        method: 'GET',
                        success: function(sizesResponse) {
                            $('#sizesGroupu').empty();

                            sizesResponse.forEach(function(size) {
                                var sizeInput = '<div class="input-group uu mb-2" data-size-id="' + size.id + '">';
                                sizeInput += '<input type="text" class="form-control" name="sizesu[]" placeholder="Size" value="' + size.size + '">';
                                sizeInput += '<input type="number" class="form-control" name="coach_pricesu[]" placeholder="Coach Price" value="' + size.Coach_price + '">';
                                sizeInput += '<input type="number" class="form-control" name="store_pricesu[]" placeholder="Store Price" value="' + size.Store_price + '">';
                                sizeInput += '<input type="number" class="form-control" name="player_pricesu[]" placeholder="Player Price" value="' + size.Player_price + '">';

                                // Create select box for colors dynamically
                                var colorSelect = '<select class="form-control select2" name="colorsu[]" multiple="multiple" data-placeholder="Select Colors">';

                                // Add static color options
                                Object.keys(colorMap).forEach(function(hexCode) {
                                    var colorName = colorMap[hexCode];
                                    // Check if the color name exists in the returned data
                                    var selected = size.colors.some(function(color) {
                                        return color.color_name.toUpperCase() === hexCode.toUpperCase();
                                    });
                                    if (selected) {
                                        colorSelect += '<option value="' + hexCode + '" selected>' + colorName + '</option>';
                                    } else {
                                        colorSelect += '<option value="' + hexCode + '">' + colorName + '</option>';
                                    }
                                });

                                colorSelect += '</select>';

                                sizeInput += colorSelect;

                                sizeInput += '<div class="input-group-append">';
                                sizeInput += '<button class="btn btn-outline-secondary add-sizeu" type="button">+</button>';
                                sizeInput += '<button class="btn btn-outline-secondary remove-sizeu" type="button">-</button>';
                                sizeInput += '</div></div>';
                                $('#sizesGroupu').append(sizeInput);
                            });

                            var colorCheckboxes = '';
                            colorCheckboxes += '<label class="form-check-label">out_of_stock colors</label>';
                            colorCheckboxes += '<br>';

                            sizesResponse.forEach(function(size) {
                                colorCheckboxes += '<div class="size-colors-group">';
                                colorCheckboxes += '<label class="form-check-label">Size: ' + size.size + '</label>';
                                colorCheckboxes += '<br>';

                                size.colors.forEach(function(color) {
                                    var checked = color.out_of_stock ? 'checked' : ''; // Check the checkbox if out_of_stock is true
                                    var checkboxName = 'color_stock_' + color.id; // Unique name for each checkbox
                                    colorCheckboxes += '<div class="form-check form-check-inline">';
                                    colorCheckboxes += '<input class="form-check-input color-checkbox" data-color-id="' + color.id + '" type="checkbox" name="' + checkboxName + '" value="' + color.id + '" ' + checked + '>';
                                    colorCheckboxes += '<label class="form-check-label">' + colorMap[color.color_name] + '</label>';
                                    colorCheckboxes += '</div>';
                                });

                                colorCheckboxes += '</div><br>';
                            });

                            $('#colors_stock').html(colorCheckboxes);

                            // Show the update modal
                            $('#updateProductModal').modal('show');
                            $('.select2').select2();
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });

                });

                $(document).on('change', '.color-checkbox', function() {
                    var colorId = $(this).data('color-id');
                    var outOfStock = $(this).prop('checked') ? 1 : 0;
                    var csrfToken = $('meta[name="csrf-token"]').attr('content');

                    $.ajax({
                        url: '{{ url('/update_color_out_of_stock') }}/' + colorId,
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken // Include CSRF token in headers
                        },
                        data: {
                            out_of_stock: outOfStock
                        },
                        success: function(response) {
                            // Handle success response if needed
                        },
                        error: function(xhr, status, error) {
                            // Handle error if needed
                        }
                    });
                });


                $('.select2').select2();
                var sizeCounteru = 1;

// Function to add a new size input
                function addSizeInputu() {
                    var sizeInputu = '<div class="input-group uu mb-2">';
                    sizeInputu += '<input type="text" class="form-control" name="sizesu[]" placeholder="Size">';
                    sizeInputu += '<input type="number" class="form-control" name="coach_pricesu[]" placeholder="Coach Price">';
                    sizeInputu += '<input type="number" class="form-control" name="store_pricesu[]" placeholder="Store Price">';
                    sizeInputu += '<input type="number" class="form-control" name="player_pricesu[]" placeholder="Player Price">';
                    sizeInputu += '<select class="form-control select2" name="colorsu[]" multiple="multiple" data-placeholder="Select Colors">';

                    // Add options for selecting colors
                    var colorOptions = [
                        { value: "0xffff0000", text: "Red" },
                        { value: "0xffffa500", text: "Orange" },
                        { value: "0xfffff000", text: "Yellow" },
                        { value: "0xff00ff00", text: "Green" },
                        { value: "0xff0000ff", text: "Blue" },
                        { value: "0xff800080", text: "Purple" },
                        { value: "0xffffc0cb", text: "Pink" },
                        { value: "0xffffffff", text: "White" },
                        { value: "0xff000000", text: "Black" },
                        { value: "0xff808080", text: "Gray" },
                        { value: "0xffffd700", text: "Gold" },
                        { value: "0xffa52a2a", text: "Brown" },
                        { value: "0xffccae94", text: "cashmere" },
                        { value: "0xff66ccff", text: "sky-blue" },
                        { value: "0xffcc33ff", text: "purple" },
                        { value: "0xffc2c2d6", text: "silver" },
                    ];

                    // Append color options to the select element
                    colorOptions.forEach(function(option) {
                        sizeInputu += '<option value="' + option.value + '">' + option.text + '</option>';
                    });

                    sizeInputu += '</select>';
                    sizeInputu += '<div class="input-group-append">';
                    sizeInputu += '<button class="btn btn-outline-secondary remove-sizeu" type="button">-</button>';
                    sizeInputu += '</div>';
                    sizeInputu += '</div>';

                    // Increment the counter
                    sizeCounteru++;
                    // Append the size input to the sizes group
                    $('#sizesGroupu').append(sizeInputu);
                    $('.select2').select2();
                }
// Function to remove the last size input
                function removeSizeInputu() {
                    // Only remove if there are more than one size inputs
                    if (sizeCounteru > 1) {
                        // Decrement the counter
                        sizeCounteru--;

                        // Remove the last size input
                        $('#sizesGroupu .input-group:last-child').remove();
                    }
                }

// Event delegation to handle button clicks
                $('#sizesGroupu').on('click', '.add-sizeu', addSizeInputu);
                $('#sizesGroupu').on('click', '.remove-sizeu', removeSizeInputu);

// Initial setup: Add the first size input
                $(document).ready(function () {
                    addSizeInputu();
                    removeSizeInputu();
                });


                $('#UpdateProduct').on('click', function () {
                    var productId = $('#product_idu').val();  // Extract the product ID from where it is stored
                    var formData = new FormData($('#updateProductForm')[0]);
                    var sizesArray = [];
                    $('.uu').each(function() {
                        var sizeId = $(this).data('size-id');
                        var size = $(this).find('[name="sizesu[]"]').val();
                        var coachPrice = $(this).find('[name="coach_pricesu[]"]').val();
                        var storePrice = $(this).find('[name="store_pricesu[]"]').val();
                        var playerPrice = $(this).find('[name="player_pricesu[]"]').val();
                        var colors = $(this).find('[name="colorsu[]"]').val() || [];
                        sizesArray.push({ size_id: sizeId, size: size, coach_price: coachPrice, store_price: storePrice, player_price: playerPrice, colors: colors });
                    });

                    // Convert sizesArray to JSON and append it to formData
                    formData.append('sizes', JSON.stringify(sizesArray));

                    $.ajax({
                        type: 'POST',
                        url: '{{ url('update-product') }}',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },// Append the product ID to the URL
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (response) {
                            if (response.success) {
                                showSuccessMessage(response.success);
                                $('#updateProductModal').modal('hide');
                                $('#productstable').DataTable().ajax.reload(); // Refresh DataTable
                            } else {
                                showErrorMessage('Unexpected response received.');
                            }
                        },
                        error: function (error) {
                            // Handle error
                            console.log(error);
                        }
                    });
                });

                $(document).on('change', '.changeIsOffer', function () {
                    var productId = $(this).data('id');
                    var isActive = $(this).prop('checked');
                    $.ajax({
                        url: '{{ url('update-offer-status') }}/' + productId,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            is_active: isActive
                        },
                        success: function (response) {
                            if (response.success) {
                                // Handle success scenario
                                console.log(response.success);
                            } else {
                                // Handle error scenario
                                console.error(response.error);
                            }
                        },
                        error: function (error) {
                            // Handle error scenario
                            console.error(error);
                        }
                    });
                });
                $(document).on('click', '.delete-product', function () {
                    var productId = $(this).data('id');

                    $.ajax({
                        url: '{{ route("delete.product") }}',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            product_id: productId
                        },
                        success: function (response) {
                            if (response.success) {
                                $('#productstable').DataTable().ajax.reload(); // Refresh DataTable
                            } else {
                                showErrorMessage('Failed to delete product');
                            }
                        },
                        error: function (xhr, status, error) {
                            showErrorMessage('An error occurred while processing the request.');
                            console.error(xhr.responseText);
                        }
                    });

                });
            });
        });
    </script>
    </body>

@endsection
