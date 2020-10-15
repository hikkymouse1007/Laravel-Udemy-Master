<?php

namespace App;

use App\Scopes\LatestScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogPost extends Model
{
    // protected $table = 'blog_posts';

    use SoftDeletes;

    protected $fillable = ['title', 'content', 'user_id'];

    public function comments()
    {
        return $this->hasMany('App\Comment')->latest();
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function scopeLatest(Builder $query)
    {
        return $query->orderBy(static::CREATED_AT, 'desc');
    }

    public static function boot()
    {
        parent::boot();

        // static::addGlobalScope(new LatestScope);

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
