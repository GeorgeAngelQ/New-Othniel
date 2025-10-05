<?php

namespace App\Http\Controllers;

use App\Filters\OrderFilter;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;

class OrderController extends Controller
{
    use ApiResponseTrait;

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
        return $this->successResponse($data, 'Orders retrieved successfully');
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
        return $this->successResponse($data, 'Order retrieved successfully');
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
