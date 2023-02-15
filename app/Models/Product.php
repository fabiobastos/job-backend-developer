<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    /**
     * Define permissão para valores definidos em massa
     *
     * @var array
     */
    protected $fillable = ['name','price','description','category_id','image_url'];

    /**
     * Define as conversões no momento da serialização
     *
     * @var array
     */
    protected $casts = [
        'price' => 'float'
    ];

    /**
     * Define relação entre Product e Category
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Prepara os dados a serem retornados em um product
     *
     * @param Product $product
     * @return array
     */
    public static function productResponse(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'description' => $product->description,
            'category' => $product->category->name,
            'image_url' => $product->image_url,
        ];
    }

    /**
     * Verifica se há uma categoria com o nome da Request
     * e retorna o seu ID, senão cria uma nova e retorna o ID
     *
     * @param string $category
     * @return integer
     */
    public static function setCategory(string $category):int
    {
        if($objCategory = Category::where('name' , '=', $category)->first()){
            return $objCategory->id;
        }else{
            $newCategory = new Category;
            $newCategory->name = $category;
            $newCategory->save();
            return $newCategory->id;
        }
    }
}
