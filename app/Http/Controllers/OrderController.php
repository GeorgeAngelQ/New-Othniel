<?php

namespace App\Http\Controllers;

use App\Filters\OrderFilter;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $filter = new OrderFilter();
        $queryItems = $filter->transform($request);
        $includeUsers = $request->query('includeUsers');
        $includeOrderDetails = $request->query('includeOrderDetails');
        $orders = Order::where($queryItems);
        if ($includeUsers) {
            $orders = $orders->with('users');
        }
        if ($includeOrderDetails) {
            $orders = $orders->with('orderDetails');
        }
        $data = new OrderCollection($orders->paginate()->appends($request->query()));
        return response()->json([
            'status' => 'success',
            'message' => 'Orders retrieved successfully',
            'data' => $data,
            'error' => null
        ], 200);
    }
    public function create()
    {

    }
    public function store(StoreOrderRequest $request)
    {

    }
    public function show(Request $request, Order $order)
    {
        $includeUsers = $request->query('includeUsers');
        $includeOrderDetails = $request->query('includeOrderDetails');
        if ($includeUsers) {
            $order->load('users');
        }
        if ($includeOrderDetails) {
            $order->load('orderDetails');
        }
        $data = new OrderResource($order);
        return response()->json([
        'status' => 'success',
        'message' => 'Order retrieved successfully',
        'data' => $data,
        'error' => null
        ], 200);
    }
    public function edit(Order $order)
    {

    }
    public function update(UpdateOrderRequest $request, Order $order)
    {

    }
    public function destroy(Order $order)
    {

    }
}
