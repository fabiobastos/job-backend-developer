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

}
