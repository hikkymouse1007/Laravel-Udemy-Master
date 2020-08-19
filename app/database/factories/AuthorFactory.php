<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Author;
use App\Profile;
use Faker\Generator as Faker;

$factory->define(Author::class, function (Faker $faker) {
    return [
        //
    ];
});

$factory->afterCreating(Author::class, function($author, $faker) {
    $author->profile()->save(factory(Profile::class)->make());
});

$factory->afterMaking(Author::class, function($author, $faker) {
    $author->profile()->save(factory(Profile::class)->make());
});
