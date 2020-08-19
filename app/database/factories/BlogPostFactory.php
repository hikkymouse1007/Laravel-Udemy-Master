<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\BlogPost;
use Faker\Generator as Faker;

$factory->define(BlogPost::class, function (Faker $faker) {
    return [
        'title' => $faker->sentence(10),
        'content' => $faker->paragraphs(5, true)

    ];
});

// defineで定義した戻り値のパラメータを定義したパラメータで上書きする 指定のないパラメータはdefineのパラメータが適応される
$factory->state(BlogPost::class, 'new-title' , function (Faker $faker) {
    return [
        'title' => 'New title',
        'content' => 'Content of the blog post'
    ];
});
