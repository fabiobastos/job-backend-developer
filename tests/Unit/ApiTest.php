<?php

namespace Tests\Unit;

use Tests\TestCase;
use Database\Factories\ProductFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ApiTest extends TestCase
{

    use DatabaseMigrations;
    use RefreshDatabase;

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_if_api_returns_200()
    {
        $response = $this->get('/api/products');
        $response->assertStatus(200);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_if_api_returns_2_products()
    {
        $fakeData = new ProductFactory;
        $this->post('/api/products', $fakeData->definition());
        $this->post('/api/products', $fakeData->definition());

        $response = $this->get('/api/products');

        $this->assertCount(2,$response->json());
    }

    /**
     * Testando a criação de um product com o payload completo
     *
     * @return void
     */
    public function test_if_api_returns_201_with_correct_post_payload()
    {
        $fakeData = new ProductFactory;
        $response = $this->post('/api/products', $fakeData->definition());

        $response->assertStatus(201);
    }

    /**
     * Testando quando o post envia um 'name' já existente
     *
     * @return void
     */
    public function test_if_api_blocks_update_returning_400_on_existing_name()
    {
        $fakeData = new ProductFactory;
        $payload = $fakeData->definition();
        $this->post('/api/products', $payload);

        $fakedata2 = new ProductFactory;
        $payloadWithError = $fakedata2->definition();
        $payloadWithError['name'] = $payload['name'];
        $response = $this->post('/api/products', $payloadWithError);

        $response->assertStatus(400);
    }

    /**
     * Testando update de um product
     *
     * @return void
     */
    public function test_if_api_updates_product()
    {
        $fakeData = new ProductFactory;
        $payload = $fakeData->definition();
        $productToBeUpdated = $this->post('/api/products', $payload);

        $fakedata2 = new ProductFactory;
        $newNameForProduct = $fakedata2->definition();
        $response = $this->put(
            '/api/products/'.$productToBeUpdated['id'],
            ['name'=>$newNameForProduct['name']]
        );

        $response->assertStatus(200);
    }

    /**
     * Testando remoção de imagem de um produto
     *
     * @return void
     */
    public function test_if_api_updates_removing_image()
    {
        $fakeData = new ProductFactory;
        $payload = $fakeData->definition();
        $productToBeUpdated = $this->post('/api/products', $payload);

        $response = $this->put(
            '/api/products/'.$productToBeUpdated['id'],
            ['image_url'=>null]
        );
        $this->assertNull($response->json()['image_url']);
    }

    /**
     * Testando deleção de um produto
     *
     * @return void
     */
    public function test_if_api_deletes_product()
    {
        $fakeData = new ProductFactory;
        $payload = $fakeData->definition();
        $productToBeDeleted = $this->post('/api/products', $payload);

        $response = $this->delete(
            '/api/products/'.$productToBeDeleted['id']
        );
        $response->assertStatus(204);
    }
}
