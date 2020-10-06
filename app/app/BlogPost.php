<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogPost extends Model
{
    // protected $table = 'blog_posts';

    use SoftDeletes;

    protected $fillable = ['title', 'content'];

    public function comments()
    {
        return $this->hasMany('App\Comment');
    }

    public static function boot()
    {
        parent::boot();

        //　子モデルも削除する(CommentモデルにSoftDeletes定義済み)
        static::deleting(function (BlogPost $blogPost){
            $blogPost->comments()->delete();
        });

        // 子モデルも復元する
        static::restoring(function (BlogPost $blogPost){
            $blogPost->comments()->restore();
        });
    }
}
