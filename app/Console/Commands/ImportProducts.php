<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ImportProductsService;
use Illuminate\Http\Client\RequestException;

use function PHPUnit\Framework\isInstanceOf;

class ImportProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:import  {--id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from https://fakestoreapi.com/products';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param ImportProductsService $service
     * @return int
     */
    public function handle(ImportProductsService $service)
    {
        $productId = $this->option('id');
        $products = $service->import(intval($productId));

        if(is_array($products)){
            $qtdProducts = $productId? 1 : count($products);
            $bar = $this->output->createProgressBar($qtdProducts);

            $this->line('Starting to GET Products from API.');
            $bar->start();

            $tableReturn = ['errors' => 0];
            if($productId){
                $preparedProduct = $service->prepareProduct($products);
                $product = $service->storeProducts($preparedProduct);
                $tableReturn['products'][] = [$product['status'],$product['content']];
                if($product['status'] !== "success"){
                    $tableReturn['errors']++;
                }
                $bar->advance();
            }else{
                foreach($products as $apiProduct){
                    $preparedProduct = $service->prepareProduct($apiProduct);
                    $product = $service->storeProducts($preparedProduct);
                    $tableReturn['products'][] = ['status' => $product['status'],'content' => $product['content']];
                    if($product['status'] !== "success"){
                        $tableReturn['errors']++;
                    }
                    $bar->advance();
                }
            }
            $bar->finish();
            $this->newLine(2);
            if($tableReturn['errors'] > 0){
                $this->error('Products importing proccess finished with errors.');
            }else{
                $this->info('Products importing proccess finished successfully.');
            }
            $this->newLine(2);
            print_r($tableReturn);
        }
    }

}
