<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserCollection;
use App\Filters\UserFilter;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $filter = new UserFilter();
        $queryItems = $filter->transform($request);
        $includeCarts = $request->query('includeCarts');
        $includeOrders = $request->query('includeOrders');
        $users = User::where($queryItems);
        if ($includeCarts) {
            $users = $users->with('carts');
        }
        if ($includeOrders) {
            $users = $users->with('orders');
        }
        $data = new UserCollection($users->paginate()->appends($request->query()));
        return response()->json([
            'status' => 'success',
            'message' => 'Users retrieved successfully',
            'data' => $data,
            'error' => null
        ], 200);
    }
    public function create()
    {

    }
    public function store(StoreUserRequest $request)
    {

    }
    public function show(Request $request, User $user)
    {
        $includeCarts = $request->query('includeCarts');
        $includeOrders = $request->query('includeOrders');
        if ($includeCarts) {
            $user->load('carts');
        }
        if ($includeOrders) {
            $user->load('orders');
        }
        $data = new UserResource($user);
        return response()->json([
        'status' => 'success',
        'message' => 'User retrieved successfully',
        'data' => $data,
        'error' => null
        ], 200);
    }
    public function edit(User $user)
    {

    }
    public function update(UpdateUserRequest $request, User $user)
    {

    }
    public function destroy(User $user)
    {

    }
}
