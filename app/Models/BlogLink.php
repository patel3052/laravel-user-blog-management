<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogLink extends Model
{
    protected $fillable = ['blog_id', 'title', 'url'];
    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }
}
