<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTitleContentToBlogpostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    # phpunitでsqliteを使用するときはnullの制約に注意
    # https://qiita.com/sola-msr/items/80b0c0e0edb67a35d282
    public function up()
    {
        Schema::table('blogposts', function (Blueprint $table) {
            $table->string('title')->default('');

            if (env('DB_CONNECTION') === 'sqlite_testing') {
                $table->text('content')->default('');
            } else {
                $table->text('content');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('blogposts', function (Blueprint $table) {
            $table->dropColumn(['title', 'content']);
        });
    }
}
