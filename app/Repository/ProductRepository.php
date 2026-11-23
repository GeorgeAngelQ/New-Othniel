<?php

namespace App\Repository;

use App\Models\Product;
use Illuminate\Support\Collection;

class ProductRepository
{
    public function findById(int $id): ?Product
    {
        return Product::find($id);
    }

    public function getAll(): Collection
    {
        return Product::all();
    }

    public function update(int $id_product, array $data): Product
    {
        Product::where('id_product', $id_product)->update($data);
        return $this->findById($id_product);
    }

    public function delete(int $id_product): void
    {
        Product::where('id_product', $id_product)->delete();
    }
}
