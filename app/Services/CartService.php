<?php

namespace App\Services;

use App\Models\Cart;
use App\Repository\CartRepository;
use App\Enums\CartStatus;

class CartService
{
    private CartRepository $cartRepository;

    public function __construct(CartRepository $cartRepository)
    {
        $this->cartRepository = $cartRepository;
    }

    public function getMyCart(int $id_user): Cart
    {
        return $this->cartRepository->findByUserIdWithDetails($id_user);
    }

    public function findOrCreateCartByUserId(int $id_user): Cart
    {
        $cart = $this->cartRepository->findByUserId($id_user);

        if ($cart === null) {
            $cart = $this->cartRepository->createCart([
                'id_user' => $id_user,
                'total' => 0,
                'status' => CartStatus::ACTIVE->value
            ]);
        }
        return $cart;
    }

    public function updateCart(int $id_user, string $status): Cart
    {
        $cart = $this->cartRepository->findByUserId($id_user);
        $this->cartRepository->updateCart($cart->id_cart, ['status' => $status]);
        $cart->status = $status;

        return $cart;
    }

    public function deleteCart(int $id_user): void
    {
        $cart = $this->cartRepository->findByUserId($id_user);
        $this->cartRepository->updateCart($cart->id_cart, ['status' => CartStatus::CANCELLED->value]);
    }

    public function recalculateTotal(Cart $cart): void
    {
        $total = $cart->cartDetails()->sum('subtotal');
        $this->cartRepository->updateCart($cart->id_cart, ['total' => $total]);
    }
}
