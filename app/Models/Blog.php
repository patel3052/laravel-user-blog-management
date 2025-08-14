<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Blog extends Model
{
    protected $fillable = ['user_id', 'title', 'description', 'slug'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(BlogImage::class);
    }

    public function links()
    {
        return $this->hasMany(BlogLink::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    protected static function booted()
    {
        static::creating(function ($blog) {
            $blog->slug = Str::slug($blog->title);
        });

        static::updating(function ($blog) {
            $blog->slug = Str::slug($blog->title);
        });
    }
}
