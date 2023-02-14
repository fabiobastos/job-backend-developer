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
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => floatval($product->price),
                'description' => $product->description,
                'category' => $product->category->name,
                'image_url' => $product->image_url,
            ];
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

        if($category = Category::where('name' , '=', $request->category)->first()){
            $product->category_id = $category->id;
        }else{
            $newCategory = new Category;
            $newCategory->name = $request->category;
            $newCategory->save();
            $product->category_id = $newCategory->id;
        }

        $product->save();

        return response($product,201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product): Response
    {
        return response([
            'id' => $product->id,
            'name' => $product->name,
            'price' => floatval($product->price),
            'description' => $product->description,
            'category' => $product->category->name,
            'image_url' => $product->image_url,
        ],200);
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
            if($category = Category::where('name' , '=', $request->category)->first()){
                $product->category_id = $category->id;
            }else{
                $newCategory = new Category;
                $newCategory->name = $request->category;
                $newCategory->save();
                $product->category_id = $newCategory->id;
            }
        }
        $product->save();

        return response([
            'id' => $product->id,
            'name' => $product->name,
            'price' => floatval($product->price),
            'description' => $product->description,
            'category' => $product->category->name,
            'image_url' => $product->image_url,
        ]);
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
        }else{
            return response('',404);
        }
    }
}
