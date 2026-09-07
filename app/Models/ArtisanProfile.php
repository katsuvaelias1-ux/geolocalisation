<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class ArtisanProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'category_id', 'slug', 'profession', 'description',
        'experience_years', 'whatsapp', 'commune', 'quartier', 'address',
        'latitude', 'longitude', 'availability', 'verification_status', 'cover_image',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'experience_years' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class, 'artisan_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'artisan_id');
    }

    public function portfolioItems(): HasMany
    {
        return $this->hasMany(PortfolioItem::class, 'artisan_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'artisan_id');
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class, 'artisan_id');
    }

    public function coverImageUrl(): ?string
    {
        if (! $this->cover_image) {
            return null;
        }

        return str_starts_with($this->cover_image, 'http')
            ? $this->cover_image
            : Storage::url($this->cover_image);
    }
}
