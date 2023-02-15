<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Response;
use App\Http\Requests\ProductRequest;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): Response
    {
        $products = Product::with('category')->get()->map(function($product){
            return Product::productResponse($product);
        });
        return response($products,200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request): Response
    {
        $product = new Product;
        $product->name = $request->name;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->image_url = $request->image_url ?: null;
        $product->category_id = Product::setCategory($request->category);
        $product->save();

        return response(Product::productResponse($product),201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product): Response
    {
        return response(Product::productResponse($product),200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\ProductRequest  $request
     * @param  \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, Product $product): Response
    {
        $product->name = $request->name ?: $product->name;
        $product->price = $request->price ?: $product->price;
        $product->description = $request->description ?: $product->description;
        $product->image_url = $request->image_url ?: $product->image_url;

        if($request->category){
            $product->category_id = Product::setCategory($request->category);
        }
        $product->save();

        return response(Product::productResponse($product));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product): Response
    {
        if($product->delete()){
            return response([],204) ;
        }
    }
}
