<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\order;
use App\Models\orderdetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class AdminOrdersController extends Controller
{
    public function get_orders(Request $request)
    {
        if ($request->ajax()) {
            $data = order::select(
                'orders.id',
                'orders.payment_way',
                'users.first_name as UserName',
                'orders.amount as OrderAmount',
                'users.phone_number as Phone_number',
                'orders.status as OrderStatus',
                'orders.created_at as date',
            )
                ->join('users', 'orders.user_id', '=', 'users.id')
                ->join('orderdetails', 'orders.id', '=', 'orderdetails.order_id')
                ->join('products', 'orderdetails.product_id', '=', 'products.id')
                ->join('sizes', 'orderdetails.sizeid', '=', 'sizes.id')
                ->groupBy('orders.id', 'users.first_name', 'orders.amount', 'users.phone_number', 'orders.status','orders.payment_way','orders.created_at') // Include non-aggregated columns in GROUP BY
                ->where('orders.status', '!=', 'canceled')
                ->where('orders.status', '!=', 'Delivered')
                ->get();

            return DataTables::of($data)
                ->addColumn('status', function ($row) {
                    return '<div class="custom-select">
                    <select class="form-select" onchange="updateOrderStatus(' . $row->id . ', this.value)">
                        <option value="Pending" ' . ($row->OrderStatus === 'Pending' ? 'selected' : '') . '>Pending</option>
                        <option value="Delivering" ' . ($row->OrderStatus === 'Delivering' ? 'selected' : '') . '>Delivering</option>
                        <option value="Delivered" ' . ($row->OrderStatus === 'Delivered' ? 'selected' : '') . '>Delivered</option>
                        <option value="waiting payment" ' . ($row->OrderStatus === 'waiting payment' ? 'selected' : '') . '>waiting payment</option>
                        <option value="canceled" ' . ($row->OrderStatus === 'canceled' ? 'selected' : '') . '>canceled</option>
                    </select>
                </div>';
                }) ->addColumn('Details', function ($row) {
                    return '<button class="btn btn-sm btn-primary details-btn" data-order-id="' . $row->id . '">Details</button>';
                })
                ->rawColumns(['status','Details'])
                ->make(true);
        }

        return view('Dashboard.orders');
    }

    public function get_cancel_orders(Request $request)
    {
        if ($request->ajax()) {
            $data = order::query()->select(
                'orders.id',
                'orders.payment_way',
                'users.first_name as UserName',
                'orders.amount as OrderAmount',
                'users.phone_number as Phone_number',
                'orders.status as OrderStatus',
                'orders.created_at as date',
            )
                ->join('users', 'orders.user_id', '=', 'users.id')
                ->join('orderdetails', 'orders.id', '=', 'orderdetails.order_id')
                ->join('products', 'orderdetails.product_id', '=', 'products.id')
                ->join('sizes', 'orderdetails.sizeid', '=', 'sizes.id')
                ->where('orders.status', 'canceled') // Filter orders by status "canceled"
                ->groupBy('orders.id', 'users.first_name', 'orders.amount', 'users.phone_number', 'orders.status','orders.payment_way','orders.created_at') // Include non-aggregated columns in GROUP BY
                ->get();

            return DataTables::of($data)
                ->addColumn('status', function ($row) {
                    return '<div class="custom-select">
                <select class="form-select" onchange="updateOrderStatus(' . $row->id . ', this.value)">
                    <option value="Pending" ' . ($row->OrderStatus === 'Pending' ? 'selected' : '') . '>Pending</option>
                    <option value="Delivering" ' . ($row->OrderStatus === 'Delivering' ? 'selected' : '') . '>Delivering</option>
                    <option value="Delivered" ' . ($row->OrderStatus === 'Delivered' ? 'selected' : '') . '>Delivered</option>
                    <option value="waiting payment" ' . ($row->OrderStatus === 'waiting payment' ? 'selected' : '') . '>waiting payment</option>
                    <option value="canceled" ' . ($row->OrderStatus === 'canceled' ? 'selected' : '') . '>canceled</option>
                </select>
            </div>';
                }) ->addColumn('Details', function ($row) {
                    return '<button class="btn btn-sm btn-primary details-btn" data-order-id="' . $row->id . '">Details</button>';
                })
                ->rawColumns(['status','Details'])
                ->make(true);
        }

        return view('Dashboard.orders');
    }
    public function get_delivered_orders(Request $request)
    {
        if ($request->ajax()) {
            $data = order::query()->select(
                'orders.id',
                'orders.payment_way',
                'users.first_name as UserName',
                'orders.amount as OrderAmount',
                'users.phone_number as Phone_number',
                'orders.status as OrderStatus',
                'orders.created_at as date',
            )
                ->join('users', 'orders.user_id', '=', 'users.id')
                ->join('orderdetails', 'orders.id', '=', 'orderdetails.order_id')
                ->join('products', 'orderdetails.product_id', '=', 'products.id')
                ->join('sizes', 'orderdetails.sizeid', '=', 'sizes.id')
                ->where('orders.status', 'Delivered') // Filter orders by status "canceled"
                ->groupBy('orders.id', 'users.first_name', 'orders.amount', 'users.phone_number', 'orders.status','orders.payment_way','orders.created_at') // Include non-aggregated columns in GROUP BY
                ->get();

            return DataTables::of($data)
                ->addColumn('status', function ($row) {
                    return '<div class="custom-select">
                <select class="form-select" onchange="updateOrderStatus(' . $row->id . ', this.value)">
                    <option value="Pending" ' . ($row->OrderStatus === 'Pending' ? 'selected' : '') . '>Pending</option>
                    <option value="Delivering" ' . ($row->OrderStatus === 'Delivering' ? 'selected' : '') . '>Delivering</option>
                    <option value="Delivered" ' . ($row->OrderStatus === 'Delivered' ? 'selected' : '') . '>Delivered</option>
                    <option value="waiting payment" ' . ($row->OrderStatus === 'waiting payment' ? 'selected' : '') . '>waiting payment</option>
                    <option value="canceled" ' . ($row->OrderStatus === 'canceled' ? 'selected' : '') . '>canceled</option>
                </select>
            </div>';
                }) ->addColumn('Details', function ($row) {
                    return '<button class="btn btn-sm btn-primary details-btn" data-order-id="' . $row->id . '">Details</button>';
                })
                ->rawColumns(['status','Details'])
                ->make(true);
        }

        return view('Dashboard.orders');
    }
    public function updateOrderStatus(Request $request)
    {
        $orderId = $request->input('orderId');
        $status = $request->input('status');

        order::where('id', $orderId)->update(['status' => $status]);

        return response()->json(['message' => 'Order status updated successfully']);
    }

//    public function getOrderDetails($orderId)
//    {
//        $orderInfo = order::select(
//            'shippingaddresses.distnation',
//            'shippingaddresses.street',
//            'shippingaddresses.number_of_billiding',
//            'shippingaddresses.number_of_floor',
//            'shippingaddresses.number_of_flat',
//            'shippingaddresses.special_mark',
//            'governments.name as governmentName'
//        )
//            ->join('shippingaddresses', 'orders.id', '=', 'shippingaddresses.order_id')
//            ->join('governments', 'shippingaddresses.government_id', '=', 'governments.id')
//            ->where('orders.id', $orderId)
//            ->first();
//
//        $orderProducts = orderdetails::query()->with(['product.category'])->select(
//            'orderdetails.quantity',
//            'products.productName',
//            'products.subcat',
//            'orderdetails.color',
//            'sizes.size',
//            'orderdetails.price',
//            'product.category.name as categoryName'
//        )
//            ->join('products', 'orderdetails.product_id', '=', 'products.id')
//            ->join('sizes', 'orderdetails.sizeid', '=', 'sizes.id')
//            ->where('orderdetails.order_id', $orderId)
//            ->with('product')
//            ->get();
//
//        return response()->json([
//            'orderInfo' => $orderInfo,
//            'orderProducts' => $orderProducts,
//        ]);
//    }

    public function getOrderDetails($orderId)
    {
        // Fetch the order's shipping information
        $orderInfo = order::select(
            'shippingaddresses.distnation',
            'shippingaddresses.street',
            'shippingaddresses.number_of_billiding',
            'shippingaddresses.number_of_floor',
            'shippingaddresses.number_of_flat',
            'shippingaddresses.special_mark',
            'governments.name as governmentName'
        )
            ->join('shippingaddresses', 'orders.id', '=', 'shippingaddresses.order_id')
            ->join('governments', 'shippingaddresses.government_id', '=', 'governments.id')
            ->where('orders.id', $orderId)
            ->first();

        if (!$orderInfo) {
            // Return an error response if no order info is found
            return response()->json(['error' => $orderId], 404);
        }

        // Fetch the products in the order, including product details and category name
        $orderProducts = orderdetails::with(['product.category', 'size'])
            ->where('order_id', $orderId)
            ->get()
            ->map(function ($item) {
                // Accessing category name through the product relationship
                $item->categoryName = $item->product->category->name ?? 'N/A';
                // Include additional product details as needed
                $item->productName = $item->product->productName;
                $item->subcat = $item->product->subcat;
                $item->color = $item->color;
                $item->size = $item->size->size ?? 'N/A';
                $item->price = $item->price;

                return $item;
            });

        // Return the combined data as a JSON response
        return response()->json([
            'orderInfo' => $orderInfo,
            'orderProducts' => $orderProducts,
        ]);
    }
}
