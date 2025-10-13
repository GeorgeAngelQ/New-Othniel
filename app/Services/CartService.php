<?php

namespace App\Services;

use App\Models\Cart;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CartService
{
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
    public function getCartById(int $id_cart): ?Cart
    {
        return Cart::with(['users', 'cartDetails.product'])->findOrFail($id_cart);
    }
    public function createCart(array $data): Cart
    {
        return DB::transaction(function () use ($data) {
            $user = Auth::guard('sanctum')->user();

            if (!$user) {
                throw new \Exception('Usuario no autenticado');
            }

            return Cart::create([
                'id_user' => $user->id_user,
                'total' => $data['total'] ?? 0,
                'status' => $data['status'] ?? 'active',
            ]);
        });
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
