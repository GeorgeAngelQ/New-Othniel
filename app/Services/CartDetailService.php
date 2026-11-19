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
    public function addProductToCart(array $data): CartDetail
    {
        return DB::transaction(function () use ($data) {
            $cart = Cart::findOrFail($data['id_cart']);
            $product = Product::findOrFail($data['id_product']);
            $quantity = $data['quantity'];
            if ($quantity <= 0) {
                throw new \InvalidArgumentException('La cantidad debe ser mayor que cero.');
            }
            if ($quantity > $product->stock) {
                throw new \InvalidArgumentException('No hay suficiente stock para el producto solicitado.');
            }
            $unitPrice = $product->price;
            $subtotal = $unitPrice * $quantity;
            $cartDetail = CartDetail::where('id_cart', $cart->id_cart)
                ->where('id_product', $product->id_product)
                ->first();
            if ($cartDetail) {
                $cartDetail->quantity += $quantity;
                $cartDetail->subtotal = $cartDetail->quantity * $unitPrice;
                $cartDetail->save();
            } else {
                $cartDetail = CartDetail::create([
                    'id_cart' => $cart->id_cart,
                    'id_product' => $product->id_product,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'subtotal' => $subtotal,
                ]);
            }
            $this->updateCartTotal($cart);
            return $cartDetail;
        });
    }

    public function addProductToCartSecond(int $id_user, int $id_product, int $quantity)
    {
        $product = $this->productRepository->findById($id_product);
        if (!$product) throw new NotFoundException('Product not found');
        if ($product-> stock < $quantity) throw new BusinessException('Product ' . $product->name . ' has insufficient stock');

        if ($this->cartRepository->findByUserId($id_user)) {
            $cart = $this->cartRepository->findByUserId($id_user);
        } else {
            $cart = $this->cartRepository->createCart([
                'id_user' => $id_user,
                'total' => 0,
                'status' => 'active',
            ]);
        }

        


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
