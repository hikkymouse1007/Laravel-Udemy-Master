<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    // protected $table = 'blog_posts';

    protected $fillable = ['title', 'content'];
}
