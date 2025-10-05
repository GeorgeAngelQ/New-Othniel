<?php

namespace App\Http\Controllers;

use App\Filters\OrderFilter;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderCollection;
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
        return new OrderCollection($orders->paginate()->appends($request->query()));
    }
    public function create()
    {

    }
    public function store(StoreOrderRequest $request)
    {

    }
    public function show(Order $order)
    {

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
