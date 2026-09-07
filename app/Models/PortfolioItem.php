<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioItem extends Model
{
    use HasFactory;

    protected $fillable = ['artisan_id', 'title', 'description', 'image'];

    public function artisanProfile(): BelongsTo
    {
        return $this->belongsTo(ArtisanProfile::class, 'artisan_id');
    }
}
