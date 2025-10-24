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
use App\Models\Cart;
use App\Models\OrderDetail;

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
    public function create() {}
    public function store(StoreOrderRequest $request)
    {
        $order = Order::create($request->validated());
        return $this->successResponse($order, 'Order created successfully', 201);
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
    public function edit(Order $order) {}
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $order->update($request->validated());
        return $this->successResponse($order, 'Order updated successfully');
    }
    public function destroy(Order $order) {}
    public function createFromCart(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not authenticated.',
            ], 401);
        }

        $cart = Cart::where('id_user', $user->id_user)
            ->where('status', 'active')
            ->with('cartDetails.product')
            ->when($request->id_cart, function ($query) use ($request) {
                $query->where('id_cart', $request->id_cart);
            })
            ->first();

        if (!$cart || $cart->cartDetails->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'There are no products in the cart to create an order.',
                'cart' => $cart,
            ], 400);
        }

        $total = $cart->cartDetails->sum(function ($detail) {
            return $detail->quantity * $detail->unit_price;
        });

        $order = Order::create([
            'id_user' => $user->id_user,
            'id_cart' => $cart->id_cart,
            'status' => 'active',
            'total' => $total,
        ]);

        foreach ($cart->cartDetails as $detail) {
            OrderDetail::create([
                'id_order' => $order->id_order,
                'id_product' => $detail->id_product,
                'quantity' => $detail->quantity,
                'price' => $detail->unit_price,
                'subtotal' => $detail->quantity * $detail->unit_price
            ]);
        }

        $cart->update(['status' => 'completed']);

        return response()->json([
            'status' => 'success',
            'message' => 'Order created successfully from cart.',
            'order' => $order,
        ]);
    }
}
