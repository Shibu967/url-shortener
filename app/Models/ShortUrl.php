<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ShortUrl extends Model
{
    protected $fillable = ['original_url', 'short_code', 'user_id', 'company_id'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($shortUrl) {
            if (!$shortUrl->short_code) {
                do {
                    $shortUrl->short_code = Str::random(8);
                } while (self::where('short_code', $shortUrl->short_code)->exists());
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
