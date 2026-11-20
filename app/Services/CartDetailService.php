<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use App\Exceptions\BusinessRules\NotFoundException;
use App\Exceptions\BusinessRules\BusinessException;

use App\Repository\CartRepository;
use App\Repository\CartDetailRepository;
use App\Repository\ProductRepository;

class CartDetailService
{
    public CartRepository $cartRepository;
    public CartDetailRepository $cartDetailRepository;
    public ProductRepository $productRepository;

    public function __construct(
        CartRepository $cartRepository,
        CartDetailRepository $cartDetailRepository,
        ProductRepository $productRepository
    ) {
        $this->cartRepository = $cartRepository;
        $this->cartDetailRepository = $cartDetailRepository;
        $this->productRepository = $productRepository;
    }

    public function findOrCreateCartByUserId(int $id_user): int
    {
        if ($this->cartRepository->findByUserId($id_user)) {
            $cart = $this->cartRepository->findByUserId($id_user);
        } else {
            $cart = $this->cartRepository->createCart([
                'id_user' => $id_user,
                'total' => 0,
                'status' => 'active',
            ]);
        }
        $id_cart = $cart->id_cart;
        return $id_cart;
    }

    public function addProductToCart(int $id_cart, int $id_product, int $quantity)
    {
        $product = $this->productRepository->findById($id_product);
        if (!$product) throw new NotFoundException('Product not found');
        if ($product-> stock < $quantity) throw new BusinessException('Product ' . $product->name . ' has insufficient stock');

        $cartItem = $this->cartDetailRepository->findByCartIdAndProductId($id_cart, $id_product);
        if (!$cartItem) throw new NotFoundException('Product not found in cart');

        


    }


    public function updateCartDetail(CartDetail $cartDetail, array $data): CartDetail
    {
        return DB::transaction(function () use ($cartDetail, $data) {
            if (isset($data['quantity'])) {
                $cartDetail->quantity = $data['quantity'];
                $cartDetail->subtotal = $cartDetail->quantity * $cartDetail->unit_price;
            }
            $cartDetail->save();
            $cart = $cartDetail->cart ?? Cart::find($cartDetail->id_cart);
            if ($cart) {
                $this->updateCartTotal($cart);
            }
            return $cartDetail->refresh();
        });
    }

    public function removeCartDetail(CartDetail $cartDetail): void
    {
        DB::transaction(function () use ($cartDetail) {
            $cart = $cartDetail->cart;
            $cartDetail->delete();
            if ($cart) {
                $this->updateCartTotal($cart);
            }
        });
    }
    public function getCartDetails(int $id_cart): Collection
    {
        return CartDetail::with('product')
            ->where('id_cart', $id_cart)
            ->get();
    }
    private function updateCartTotal(Cart $cart): void
    {
        $total = $cart->cartDetails()->sum('subtotal');
        $cart->update(['total' => $total]);
    }
}
