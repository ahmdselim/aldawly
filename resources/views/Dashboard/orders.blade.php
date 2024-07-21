@extends('layouts.index')

@section('content')
    <body class="vertical-layout vertical-menu-modern  navbar-floating footer-static   menu-collapsed" data-open="click" data-menu="vertical-menu-modern" data-col="">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>    <!-- Include DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css">
    <!-- Include DataTables JS -->
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js" defer></script>
    <script src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js" defer></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .custom-select {
            position: relative;
            display: inline-block;
            width: 150px; /* Adjust the width as needed */
            font-size: 16px;
            color: #333;
            border: 1px solid #ccc;
            border-radius: 4px;
            overflow: hidden;
            background-color: #fff;
        }
        .light-green-row {
            background-color: #cfe5cf; /* Light green background */
        }
        /* Style for the select box arrow */
        .custom-select select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            cursor: pointer;
            padding: 10px;
            width: 100%;
            border: none;
            outline: none;
            background: transparent;
        }

        /* Style for the arrow icon */
        .custom-select::after {
            content: '\25BC';
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            pointer-events: none;
        }

        /* Hover effect for the select box */
        .custom-select:hover {
            border-color: #666;
        }
    </style>
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

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header border-bottom">
                            <h4 class="card-title">Search Orders With Export</h4>
                        </div>
                        <br />
                        <div class="card-body">
                            <form action="" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <input type="date"  id="from" class="form-control" name="from" placeholder="Order From" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <input type="date" id="to" class="form-control" name="to" placeholder="Order To" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-12">
                                        <div class="form-group">
                                            <select class="custom-select" name="status" id="status" required>
                                                <option value="all">All</option>
                                                <option value="pending">Pending</option>
                                                <option value="delivering">Delivering</option>
                                                <option value="delivered">Delivered</option>
                                                <option value="waiting payment">waiting payment</option>
                                                <option value="canceled">canceled</option>
                                            </select>
                                        </div>Add Order
                                    </div>
                                    <div class="col-md-3 col-12 d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary me-1 mb-1">Search</button>
                                    </div>
                                </div>
                                <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="detailsModalLabel">Order Details</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body" id="modalContent">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="card-header border-bottom">
                                        <h4 class="card-title">Orders Table</h4>
                                    </div>
                                    <div class="card-datatable">
                                        <table id="orderstable" class="dt-responsive table">
                                            <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>User Name</th>

                                                <th>User Phone</th>
                                                <th>payment way</th>
                                                <th>Order Amount</th>
                                                <th>Order Status</th>
                                                <th>Date</th>
                                                <th>Details</th>
                                            </tr>
                                            </thead>

                                        </table>
                                    </div>
                                </div>
                            </form>
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
                                <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="detailsModalLabel">Order Details</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body" id="modalContent">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <div class="card">
                                <div class="card-header border-bottom">
                                    <h4 class="card-title">Orders Table</h4>
                                </div>
                                <div class="card-datatable">
                                    <table id="orderstable" class="dt-responsive table">
                                        <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>User Name</th>

                                            <th>User Phone</th>
                                            <th>payment way</th>
                                            <th>Order Amount</th>
                                            <th>Order Status</th>
                                            <th>Date</th>
                                            <th>Details</th>
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
            $('#orderstable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("get.orders") }}',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                },
                order: [
                    [0, 'desc']
                ],
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'UserName', name: 'UserName' },
                    {data: 'Phone_number' , name: 'Phone_number'},
                    {data: 'payment_way' , name: 'payment_way'},
                    { data: 'OrderAmount', name: 'OrderAmount'},
                    { data: 'status', name: 'status' },
                    { data: 'date', name: 'date'},
                    { data: 'Details', name: 'Details' },
                ] ,
                rowCallback: function(row, data) {
                    var today = new Date();
                    var date = new Date(data.date); // Assuming data.date is in ISO date format like 'YYYY-MM-DD'
                    if (date.toDateString() === today.toDateString()) {
                        $(row).addClass('light-green-row'); // Add a custom class to the row for styling
                    }
                }
            });

            window.updateOrderStatus = function (orderId, status) {
                $.ajax({
                    url: '{{ url('update-order-status') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        orderId: orderId,
                        status: status
                    },
                    success: function (response) {
                        // Handle success response if needed
                        console.log(response);
                    },
                    error: function (error) {
                        // Handle error response if needed
                        console.error(error);
                    }
                });
            };

            function hexToColorName(hexColor) {
                switch (hexColor) {
                    case '0xffff0000':
                        return 'Red';
                    case '0xffffa500':
                        return 'Orange';
                    case '0xffffff00':
                        return 'Yellow';
                    case '0xff00ff00':
                        return 'Green';
                    case '0xff0000ff':
                        return 'Blue';
                    case '0xff800080':
                        return 'Purple';
                    case '0xffffc0cb':
                        return 'Pink';
                    case '0xffffffff':
                        return 'White';
                    case '0xff000000':
                        return 'Black';
                    case '0xff808080':
                        return 'Gray';
                    case '0xffffd700':
                        return 'Gold';
                    case '0xffa52a2a':
                        return 'Brown';
                    case '0xffccae94':
                        return 'Cashmere';
                    case '0xff66ccff':
                        return 'Sky-blue';
                    case '0xffcc33ff':
                        return 'Purple'; // Note: There are two 'Purple' entries in the original switch statement.
                    case '0xffc2c2d6':
                        return 'Silver';
                    default:
                        return hexColor;
                }
            }

            $(document).on('click', '.details-btn', function () {
                var orderId = $(this).data('order-id');
                $.ajax({
                    url: '{{ url('order/details') }}/' + orderId,
                    method: 'GET',
                    success: function (response) {
                        var modalContent = $('#modalContent');
                        modalContent.empty(); // Clear previous content

                        var orderInfo = response.orderInfo;
                        var orderProducts = response.orderProducts;

                        var shippingHtml = `
                        <div class="shipping-info">
                            <h4>Shipping Information</h4>
                            <p>Destination: ${orderInfo.distnation}</p>
                            <p>Street: ${orderInfo.street}</p>
                            <p>Building Number: ${orderInfo.number_of_billiding}</p>
                            <p>Floor Number: ${orderInfo.number_of_floor}</p>
                            <p>Flat Number: ${orderInfo.number_of_flat}</p>
                            <p>Special Mark: ${orderInfo.special_mark}</p>
                            <p>Government: ${orderInfo.governmentName}</p>
                        </div>
                        <hr>
                    `;
                        modalContent.append(shippingHtml);

                        // Loop through each product
                        $.each(orderProducts, function (index, order) {

                            var detailsHtml = `
                            <div class="product-details">
                                <p>Product Name: ${order.productName}</p>
                                <p>Category: ${order.categoryName}</p>
                                <p>Subcategory: ${order.subcat}</p>
                                <p>Size: ${order.size}</p>
                                <p>Color: ${hexToColorName(order.color)}</p>
                                <p>Price: ${order.price}</p>
                                <p>Quantity: ${order.quantity}</p>
                            </div>
                            <hr>
                        `;
                            modalContent.append(detailsHtml);
                        });

                        $('#detailsModal').modal('show');
                    },
                    error: function (error) {
                        console.error(error);
                    }
                });
            });


        });
    </script>
