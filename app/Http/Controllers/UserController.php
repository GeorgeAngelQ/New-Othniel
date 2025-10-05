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
        return new UserCollection($users->paginate()->appends($request->query()));
    }
    public function create()
    {

    }
    public function store(StoreUserRequest $request)
    {

    }
    public function show(User $user)
    {
        return new UserResource($user);
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
