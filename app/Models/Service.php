<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['artisan_id', 'category_id', 'name', 'description', 'price_min', 'price_max'];

    protected function casts(): array
    {
        return ['price_min' => 'decimal:2', 'price_max' => 'decimal:2'];
    }

    public function artisanProfile(): BelongsTo
    {
        return $this->belongsTo(ArtisanProfile::class, 'artisan_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
