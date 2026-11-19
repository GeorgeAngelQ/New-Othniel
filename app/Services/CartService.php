<?php

namespace App\Services;

use App\Models\Cart;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use App\Repository\CartRepository;

class CartService
{
    public CartRepository $cartRepository;

    public function __construct(CartRepository $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    public function getAllCarts(array $filters = []): Collection
    {
         $query = Cart::query();

        // Si no se pasa un filtro, usar el usuario autenticado
        if (isset($filters['id_user'])) {
            $query->where('id_user', $filters['id_user']);
        } else {
            $user = Auth::guard('sanctum')->user();
            if ($user) {
                $query->where('id_user', $user->id_user);
            }
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->with(['users', 'cartDetails'])->get();
    }

    public function getCartByUserId(int $id_user): ?Cart
    {
        return $this->cartRepository->getCartByUserId($id_user);
    }

    public function createCart(): Cart
    {
        $user = auth('sanctum')->user();
        return $this->cartRepository->createCart([
            'id_user' => $user->id_user,
            'total' => 0,
            'status' => 'active',
        ]);
    }

    public function updateCart(Cart $cart, array $data): Cart
    {
        return DB::transaction(function () use ($cart, $data) {
            $cart->update($data);
            return $cart;
        });
    }
    public function deleteCart(Cart $cart): void
    {
        $cart->delete();
    }
    public function recalculateTotal(Cart $cart): void
    {
        $total = $cart->cartDetails()->sum('subtotal');
        $cart->update(['total' => $total]);
    }
}
