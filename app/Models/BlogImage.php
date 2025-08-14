<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogImage extends Model
{
    protected $fillable = ['blog_id', 'path', 'original_name'];
    public function blog()
    {
        return $this->belongsTo(Blog::class);
    }
}
