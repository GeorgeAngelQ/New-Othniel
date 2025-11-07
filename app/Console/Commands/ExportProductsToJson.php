<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ExportProductsToJson extends Command
{
    protected $signature = 'products:export';
    protected $description = 'Exporta los productos a catalog.json';

    public function handle()
    {
        $products = Product::all();

        Storage::disk('local')->put('catalog.json', $products->toJson(JSON_PRETTY_PRINT));

        $this->info('✅ Productos exportados a storage/app/catalog.json');
    }
}
