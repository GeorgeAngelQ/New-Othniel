<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserCollection;
use App\Filters\UserFilter;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ApiResponseTrait;

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
        return $this->successResponse($data, 'Users retrieved successfully');
    }
    public function create()
    {

    }
    public function store(StoreUserRequest $request)
    {
        $user = User::create($request->validated());
        return $this->successResponse($user, 'User created successfully', 201);
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
        return $this->successResponse($data, 'User retrieved successfully');
    }
    public function edit(User $user)
    {

    }
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        $user->update($data);
        return $this->successResponse($user, 'Usuario actualizado correctamente');
    }
    public function destroy(User $user)
    {

    }
}
