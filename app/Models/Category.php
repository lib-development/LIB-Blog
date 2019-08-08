<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    public $table = 'categories';
    public $increments = true;

    protected $fillable = ['id', 'slug', 'name', 'description', 'author_id'];

    public function blogPosts()
    {
        return $this->hasMany('App\Models\BlogContent', 'category_id');
    }

    public function owner()
    {
        return $this->belongsTo('App\Models\User', 'author_id');
    }
}
