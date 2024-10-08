<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'author_id',
        'short_title',
        'title',
        'content',
        'thumbnail',
        'status',
    ];

    public function staff(){
        return $this->belongsTo(Staff::class,'author_id');
    }

    public function postCatalogs(){
        return $this->belongsToMany(PostCatalog::class, 'post_catalog_links');
    }
}
