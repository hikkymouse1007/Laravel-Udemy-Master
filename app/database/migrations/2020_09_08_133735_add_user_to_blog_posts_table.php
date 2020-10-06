<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
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
        // Schema::disableForeignKeyConstraints();
        // // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropForeign('blog_posts_user_id_foreign');
            $table->dropColumn('user_id');
        });
        // Schema::enableForeignKeyConstraints();
    }
}
