<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use SoftDeletes;

    // blog_post_id by default
    public function blogPost()
    {
        return $this->belongsTo('App\BlogPost');
    }
}
