<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductCollection;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return ProductCollection
     */
    public function index(Request $request): ProductCollection
    {
        $query = Product::with('category');
        $query->when($request->search, function ($query,$search) {
            return $query->where('name', 'like', '%'.$search.'%')
                ->orWhereRelation('category', 'name', 'like', '%'.$search.'%');
        });
        $query->when($request->category, function ($query,$search) {
            return $query->whereRelation('category', 'name', 'like', '%'.$search.'%');
        });
        $query->when($request->with_image, function ($query,$bool) {
            return $bool === 'true' ?  $query->whereNotNull('image_url') : $query->whereNull('image_url');
        });
        $query->when($request->product_id, function ($query,$id) {
            return $query->where('id', '=', intval($id));
        });

        return new ProductCollection($query->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request): ProductResource
    {
        $product = new Product;
        $product->name = $request->name;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->image_url = $request->image_url ?: null;
        $product->category_id = Category::firstOrCreate(['name' => $request->category])->id;
        $product->save();

        return new ProductResource($product);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product $product
     * @return ProductResource
     */
    public function show(Product $product): ProductResource
    {
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\ProductRequest  $request
     * @param  \App\Models\Product $product
     * @return ProductResource
     */
    public function update(ProductRequest $request, Product $product): ProductResource
    {
        $product->name = $request->name ?: $product->name;
        $product->price = $request->price ?: $product->price;
        $product->image_url = $request->image_url ?: $product->image_url;
        $product->description = $request->description ?: $product->description;

        if($request->category){
            $product->category_id = Category::firstOrCreate(['name' => $request->category])->id;
        }
        $product->save();

        return new ProductResource($product);
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
