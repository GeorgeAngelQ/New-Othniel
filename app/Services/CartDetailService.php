<?php

namespace App\Services;

use App\Models\CartDetail;
use App\Exceptions\BusinessRules\BusinessException;
use App\Models\Cart;
use App\Repository\CartDetailRepository;
use App\Repository\ProductRepository;
use App\Repository\LvlTransactionManager;

class CartDetailService
{
    private CartDetailRepository $cartDetailRepository;
    private ProductRepository $productRepository;
    private LvlTransactionManager $transactionManager;

    public function __construct(
        CartDetailRepository $cartDetailRepository,
        ProductRepository $productRepository,
        LvlTransactionManager $transactionManager
    ) {
        $this->cartDetailRepository = $cartDetailRepository;
        $this->productRepository = $productRepository;
        $this->transactionManager = $transactionManager;
    }

    public function addProductToCart(Cart $cart, int $id_product, int $quantity)
    {
        return $this->transactionManager->transaction(
            function () use ($cart, $id_product, $quantity)
            {
                $product = $this->productRepository->findById($id_product);
                $productCartDetail = $this->cartDetailRepository->findByCartIdAndProductId($cart->id_cart, $id_product);
                if ($productCartDetail-> quantity + $quantity > $product->stock) throw new BusinessException('Product ' . $product->name . ' has insufficient stock');
                if ($productCartDetail) {
                    $this->cartDetailRepository->update($productCartDetail,[
                        'quantity' => $productCartDetail->quantity + $quantity,
                        'unit_price' => $productCartDetail->unit_price,
                        'subtotal' => ($productCartDetail->quantity + $quantity) * $productCartDetail->unit_price,
                    ]);
                } else{
                    $this->cartDetailRepository->create([
                        'id_cart' => $cart->id_cart,
                        'id_product' => $id_product,
                        'quantity' => $quantity,
                        'unit_price' => $product->price,
                        'subtotal' => $quantity * $product->price
                    ]);
                }
            }
        );
    }

    public function updateQuantity(int $id_cart, int $id_product, int $quantity): CartDetail
    {
        return $this->transactionManager->transaction(
            function () use ($id_cart, $id_product, $quantity) {
                $productCartDetail = $this->cartDetailRepository->findByCartIdAndProductId($id_cart, $id_product);
                $product = $this->productRepository->findById($id_product);
                if ($quantity > $product->stock) {
                    throw new BusinessException('Product ' . $product->name . ' has insufficient stock.');
                }
                $this->cartDetailRepository->update($productCartDetail, [
                    'quantity' => $quantity,
                    'subtotal' => $quantity * $productCartDetail->unit_price,
                ]);
                return $productCartDetail;
            });
    }

    public function delete(int $id_cart, int $id_product): void
    {
        $cartDetail = $this->cartDetailRepository->findByCartIdAndProductId($id_cart, $id_product);
        if ($cartDetail !== null) {
            $this->cartDetailRepository->delete($cartDetail->id_cart_detail);
        }
    }

}
