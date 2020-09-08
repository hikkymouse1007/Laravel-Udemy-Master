<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserToBlogPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    // Laravel 7.x系の書き方
    public function up()
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            // $table->foreignId('user_id')->nullable->constrained();

            // テストデータベースがsqliteの時はdefaultを入れる必要がある(constrained()の前に設定)
            if (env('DB_CONNECTION') === 'sqlite_testing') {
                $table->foreignId('user_id')->default(0)->constrained();
            } else {
                $table->foreignId('user_id')->constrained();
            }
        });
    }

    /**
     * Reverse the migrations.
     *'
     * @return void
     */
    public function down()
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropForeign('user_id');

        });
    }
}
