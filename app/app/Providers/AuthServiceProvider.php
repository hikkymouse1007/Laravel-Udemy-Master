<?php

namespace App\Providers;

use App\BlogPost;
use App\Policies\BlogPostPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
        'App\BlogPost' => 'App\Policies\BlogPostPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('home.secret', function ($user) {
            return $user->is_admin;
        });

        // Gate::define('update-post', function ($user, $post) {
        //     return $user->id == $post->user_id;
        // });
        //Gate:allows('update-post', $post);
        //$this->authorize('update-post', $post);

        // Gate::define('delte-post', function ($user, $post) {
        //     return $user->id == $post->user_id;
        // });

        // Gate::define('post.update', 'App\Policies\BlogPostPolicy@update');
        // Gate::define('post.delete', 'App\Policies\BlogPostPolicy@delete');

        // resource: post.create, post.view, post.update, post.deleteを提供
        // モデル名を指定してPolicyを作った場合のデフォルト名で提供
        // $ php artisan make:policy BlogPostPolicy --model=BlogPost

        // Gate::resource('posts', 'App\Policies\BlogPostPolicy');
        // commentsテーブルの場合
        // comments.create, comments.update etc...

        Gate::before(function ($user, $ability) {
            if ($user->is_admin && in_array($ability, ['update'])) {
                return true;
            }
        });

        // Gate::after(function ($user, $ability, $result) {
        //     if ($user->is_admin)  {
        //         return true;
        //     }
        // });
    }
}
