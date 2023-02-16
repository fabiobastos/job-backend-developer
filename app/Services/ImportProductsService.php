<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Http\Requests\ProductRequest;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Client\RequestException;


class ImportProductsService {


    /**
     * Importa os produtos da FakeStoreApi
     *
     * @param integer $id_option
     * @return boolean|RequestException
     */
    public function import(int $id_option):RequestException|array
    {
        $id = $id_option ?: "";
        $response = Http::get('https://fakestoreapi.com/products/'.$id);
        if($response->successful()){
            return $response->json();
        }else{
            $response->throw();
        }
    }

    /**
     * Grava o produto no banco de dados se válido
     *
     * @param array $arrProduct
     * @return array|boolean
     */
    public function storeProducts(array $arrProduct):array|bool
    {
        $request = new ProductRequest($arrProduct);
        $validator = validator($arrProduct, $request->rules(),);

        if($validator->fails()){
            return $this->retornoPadrao(
                'validation-error',
                $validator->errors()->messages(),
                $arrProduct
            );
        }else{
            $productController = new ProductController;
            $product = $productController->store($request);

            return $this->retornoPadrao(
                'success',
                $product->toArray($product)
            );
        }
    }

    /**
     * Prepara os dados do produto para o request
     *
     * @param array $apiProduct
     * @return array
     */
    public function prepareProduct(array $apiProduct): array
    {
        $arrProduct = [
            "name" => $apiProduct['title'],
            "price" => $apiProduct['price'],
            "description" => $apiProduct['description'],
            "image_url" => $apiProduct['image'],
            "category" => $apiProduct['category']
        ];
        return $arrProduct;
    }

    /**
     * Padroniza o retorno dos produtos enviados para storeProducts()
     *
     * @param string $status
     * @param array|null $content
     * @param array|string|null $payload
     * @return array
     */
    private function retornoPadrao(
        string $status,
        array $content = null,
        array|string $payload = null
    ):array
    {
        $response = [
            'status' => $status,
            'content' => $content,
            'payload' => $payload

        ];
        return $response;
    }


}
