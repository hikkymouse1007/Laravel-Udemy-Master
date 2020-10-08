## 76
seedingごとmigrate:fresh実行

```
php artisan migrate:fresh --seed
```

## 77
seedingのモデルの違いについて

```
$oliver = factory(App\User::class)->state('oliver')->create(); //stateメゾッド
$else = factory(App\User::class, 20)->create(); // 20ユーザの追加
dd(get_class($oliver), get_class($else));

=> 
"App\User"
"Illuminate\Database\Eloquent\Collection"

$users = $else->concat([$oliver]);
dd($users->count());
=> 21

```

# 78

seederファイルの作成

```
php artisan make:seeder UsersTableSeeder
```

特定のクラスのみのseeding
```
php artisan db:seed --class=UsersTableSeeder
```

# 79 
app/vendor/laravel/framework/src/Illuminate/Database/Seeder.php

$command変数の設定
```
abstract class Seeder
{
    
    protected $command;
```

```
// app/database/seeds/DatabaseSeeder.php

if ($this->command->confirm('Do you want to fresh the database?')) {
            $this->command->call('migrate:fresh');
            $this->command->info('Database was refreshed');
        }
```

実行

```
php artisan db:seed

 Do you want to fresh the database? (yes/no) [no]:
 > no

Seeding: UsersTableSeeder
Seeded:  UsersTableSeeder (0.18 seconds)
Seeding: BlogPostsTableSeeder
Seeded:  BlogPostsTableSeeder (0.16 seconds)
Seeding: CommentsTableSeeder
Seeded:  CommentsTableSeeder (0.47 seconds)

php artisan db:seed

 Do you want to fresh the database? (yes/no) [no]:
 > yes

Dropped all tables successfully.
Migration table created successfully.
Migrating: 2014_10_12_000000_create_users_table

```

```
// if ($this->command->confirm('Do you want to fresh the database?', true))

php artisan db:seed

 Do you want to fresh the database? (yes/no) [yes]: <= デフォルトがyesになる 
```

commandを実行しない

```
php artisan db:seed -n
```

TODO: migrate:refreshでエラーが出てしまう
=> 解決
- renameに関するエラーはdrop()ではなく、rename()でもどす
https://awesome-programmer.hateblo.jp/entry/2019/06/26/204015

```
public function up()
    {
        Schema::rename('blogposts', 'blog_posts');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('blog_posts',  'blogposts' );
    }
```

- 外部キーのdropは、indexから外すこと
参考:phpmyadminからインデックスを確認できる(またはmysqlのshow index)
![スクリーンショット 2020-10-05 23 57 57](https://user-images.githubusercontent.com/54907440/95096803-b420b380-0767-11eb-8577-d7bfa7362a0c.png)


```
mysql > show index from blog_posts;
+------------+------------+----------------------------+--------------+-------------+-----------+-------------+----------+--------+------+------------+---------+---------------+
| Table      | Non_unique | Key_name                   | Seq_in_index | Column_name | Collation | Cardinality | Sub_part | Packed | Null | Index_type | Comment | Index_comment |
+------------+------------+----------------------------+--------------+-------------+-----------+-------------+----------+--------+------+------------+---------+---------------+
| blog_posts |          0 | PRIMARY                    |            1 | id          | A         |           0 |     NULL | NULL   |      | BTREE      |         |               |
| blog_posts |          1 | blog_posts_user_id_foreign |            1 | user_id     | A         |           0 |     NULL | NULL   |      | BTREE      |         |               |
+------------+------------+----------------------------+--------------+-------------+-----------+-------------+----------+--------+------+------------+---------+---------------+
```


```

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
            $table->dropColumn('user_id'); // TODO:refreshできない
        });
        // Schema::enableForeignKeyConstraints();
    }
```

# 80-81
子モデルに親モデルの外部キー制約をしているとき、
親モデルをDeleteできないときのエラー
```
SQLSTATE[23000]: Integrity constraint violation: 1451 Cannot delete or update a parent row: a foreign key constraint fails 
```

解決策

1. Model_Eventの定義
https://laravel.com/docs/8.x/eloquent#events

parant::boot();
https://laracasts.com/discuss/channels/laravel/parentboot
```
// app/app/BlogPost.php

class BlogPost extends Model
{

    public static function boot()
    {

        //親モデルにイベントが発生した時にクロージャを実行する
        parent::boot();

        //
        static::deleting(function (BlogPost $blogPost){
            $blogPost->comments()->delete();
        });
    }
}

```

2. cascade_deleteの定義

```
public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
             $table->dropForeign(['blog_post_id']);
             $table->foreign('blog_post_id')
                ->references('id')
                ->on('blog_posts')
                ->onDelete('cascade');
        });
    }
```
phpMyAdmin

![スクリーンショット 2020-10-06 22 52 06](https://user-images.githubusercontent.com/54907440/95210732-c3b30180-0826-11eb-8164-4227e45e3c97.png)

# 82
Soft delets

論理削除の設定

```
// app/database/migrations/2020_10_06_135835_add_soft_deletes_to_blog_posts_table.php
public function up()
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->softDEletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('blog_posts', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
```

カラムが追加される

![スクリーンショット 2020-10-06 23 01 29](https://user-images.githubusercontent.com/54907440/95211832-00332d00-0828-11eb-9c47-05505def4fc2.png)
データを削除してもdeleted_atが更新され、
レコードにデータは残る

## Soft delete querying

```
// php artisan tinker 
>>> $posts = BlogPost::all()->pluck('id');
=> Illuminate\Support\Collection {#3981
     all: [
       5,
       6,
       7,
       8,
       9,
       10,
       11,
       12,
       13,
       14,
       15,
       16,
       17,
       18,
       19,
       20,
       21,
       22,
       23,
       24,
       25,
       26,
       27,
       28,
       29,
       30,
       31,
       32,
       33,
       34,
       35,
       36,
       37,
       38,
       39,
       40,
       41,
       42,
       43,
       44,
       45,
       46,
       47,
       48,
       49,
       50,
     ],
   }

   $all = BlogPost::withTrashed()->get()->pluck('id');
=> Illuminate\Support\Collection {#3992
     all: [
       3,
       4,
       5,
       6,
       7,
       8,
       9,
       10,
       11,
       12,
       13,
       14,
       15,
       16,
       17,
       18,
       19,
       20,
       21,
       22,
       23,
       24,
       25,
       26,
       27,
       28,
       29,
       30,
       31,
       32,
       33,
       34,
       35,
       36,
       37,
       38,
       39,
       40,
       41,
       42,
       43,
       44,
       45,
       46,
       47,
       48,
       49,
       50,
     ],
   }

// 論理削除したインスタンスのみ取得
   $all = BlogPost::onlyTrashed()->get()->pluck('id');
=> Illuminate\Support\Collection {#3991
     all: [
       3,
       4,
       5,
       6,
       7,
       8,
       9,
     ],
   }

   $all = BlogPost::onlyTrashed()->where('id', 7)->get();
=> Illuminate\Database\Eloquent\Collection {#4017
     all: [
       App\BlogPost {#4015
         id: 7,
         created_at: "2020-10-06 13:29:08",
         updated_at: "2020-10-06 14:11:09",
         title: "Explicabo amet porro qui eum praesentium ea voluptas occaecati fuga sed quas beatae.",
         content: """
           Et et enim accusantium dolorem ipsam iusto aut ab. Ut veniam accusantium ducimus maiores autem. Nihil qui accusamus eos itaque libero rerum hic.\n
           \n
           Aut dignissimos natus consequuntur id reprehenderit. Molestias nihil et provident corrupti ab sint optio. Provident distinctio veritatis libero aliquid. Officiis vel eos et quaerat dignissimos animi nobis. Est et rerum omnis architecto aspernatur commodi.\n
           \n
           Voluptates perferendis cumque enim similique laudantium molestiae aut. Veritatis et iste numquam consequatur. Corporis iure similique illum est autem et explicabo. Est doloribus odit quo vel.\n
           \n
           In amet repellendus eum explicabo tempore et. Reiciendis consectetur qui laudantium ut dolores voluptatem. Laudantium qui quia quia sunt ipsa.\n
           \n
           Error voluptatum nulla amet et tenetur doloremque ex. Fugit aperiam saepe inventore amet. Fugiat nihil et est possimus.
           """,
         user_id: 4,
         deleted_at: "2020-10-06 14:11:09",
       },
     ],
   }
    $all = BlogPost::withTrashed()->get();
    $post = $all->find(3);
    => App\BlogPost {#4056
     id: 3,
     created_at: "2020-10-06 13:29:08",
     updated_at: "2020-10-06 14:06:41",
     title: "Velit dolores rerum accusantium ut explicabo velit omnis aut numquam id aspernatur.",
     content: """
       Nam odit beatae culpa voluptatem rerum doloribus eos ullam. Qui repellendus distinctio optio earum accusantium modi. Culpa consequuntur repellat quia perspiciatis eaque odit qui ut.\n
       \n
       In facere similique voluptatem asperiores. Ipsam magni consequatur magni veritatis dolores. Ratione aut laboriosam voluptatem occaecati molestias.\n
       \n
       Itaque autem vel id mollitia voluptas. Dolorum soluta voluptatem harum natus molestias minima. Pariatur suscipit in error inventore.\n
       \n
       Tempore odit qui eveniet. Nostrum placeat eligendi deleniti quam voluptatem eaque nobis id. Rerum illo accusantium autem ducimus fugiat nostrum. Aliquid et sunt dolores quaerat corrupti sunt enim qui.\n
       \n
       Ut quas aut et corrupti ut. Possimus ab ad blanditiis minima et doloribus architecto. Et cupiditate sequi in tempora explicabo. Neque fuga magnam cum repellendus quas animi.
       """,
     user_id: 5,
     deleted_at: "2020-10-06 14:06:41",
   }

```




### その他諸々

make:migrationについて

```
// UserModelごと
php artisan make:migration create_users_table --create=users

// Userモデルへのカラム 追加

php artisan make:migration add_votes_to_users_table --table=users

```

命名規則を作っておくと問題を解消しやすい

```
テーブル作成時
php artisan make:migration create_{テーブル名} --create={テーブル名}
例）php artisan make:migration create_users --create=users

テーブル変更時
php artisan make:migration modify_{テーブル名}{YYYYMMDD} --table={テーブル名}
例）php artisan make:migration modifyusers_20160128 --table=users
```


カラムをドロップするには、スキーマビルダのdropColumnメソッドを使用します。
SQLiteデータベースからカラムをドロップする場合は、事前にcomposer.jsonファイルへ
doctrine/dbal依存パッケージを追加してください。
その後にライブラリーをインストールするため
、ターミナルでcomposer updateを実行してください。

```
Schema::table('users', function (Blueprint $table) {
    $table->dropColumn('votes');
});
```

dropColumnメソッドにカラム名の配列を渡せば、テーブルから複数のカラムをドロップできます。

```
Schema::table('users', function (Blueprint $table) {
    $table->dropColumn(['votes', 'avatar', 'location']);
});

```

# 84
Restoring soft delete model
子モデルにも論理削除を適用する + 論理削除データを復元する

```
// migration
class AddSoftDeletesToCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}

// app/app/BlogPost.php
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

        static::deleting(function (BlogPost $blogPost){
            $blogPost->comments()->delete();
        });

        // blogpostsレコードがrestore()されたらcommentsレコードもrestore()する
        static::restoring(function (BlogPost $blogPost){
            $blogPost->comments()->restore();
        });
    }
}



// tinker 
$post = BlogPost::has('comments')->get()->first();
[!] Aliasing 'BlogPost' to 'App\BlogPost' for this Tinker session.
=> App\BlogPost {#4053
     id: 10,
     created_at: "2020-10-06 13:29:08",
     updated_at: "2020-10-06 13:29:08",
     title: "Minus illum ut asperiores dolorem autem et cumque aut minima quo.",
     content: """
       Itaque
$post->delete();
=> true

$post = BlogPost::onlyTrashed()->find(10);
=> App\BlogPost {#4091
     id: 10,
     created_at: "2020-10-06 13:29:08",
     updated_at: "2020-10-06 14:27:05",
     title: "Minus illum ut asperiores dolorem autem et cumque aut minima quo.",
     content: """
       Itaque nisi nemo non non aut. Explicabo excepturi quasi et autem asperiores 
       """,
     user_id: 10,
     deleted_at: "2020-10-06 14:27:05",

// 論理削除されたテーブルを再び保存
$post->restore();
=> true

// 強制的に物理削除
$post->forceDelete();
=> true

```

# 85 
Testing soft deleted models

TODO:assertSoftDeleteが通らない

# 87
Gates
Providerに定義したクロージャを実行し、
データをフィルタリングできる
> ゲートは、特定のアクションを実行できる許可が、あるユーザーにあるかを決めるクロージャのことです。通常は、App\Providers\AuthServiceProviderの中で、Gateファサードを使用し、定義します。ゲートは常に最初の引数にユーザーインスタンスを受け取ります。関連するEloquentモデルのような、追加の引数をオプションとして受け取ることもできます。
https://readouble.com/laravel/7.x/ja/authorization.html


```
// app/app/Providers/AuthServiceProvider.php
public function boot()
    {
        $this->registerPolicies();

        Gate::define('update-post', function ($user, $post) { //defineの'updata-post'の名前は任意のもの
            return $user->id == $post->user_id;
        });
    }
```

Gateを呼び出す
```
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    ~~~~

public function update(StorePost $request,$id)
    {
        $post = BlogPost::findorFail($id);

        if (Gate::denies('update-post', $post)) { //呼び出し
            abort(403, "You can't edit this blog post!!");
        }

```

# 88 
Authorize
app/app/Providers/AuthServiceProvider.php
をGateと同じように呼び出し可能
```
// app/app/Http/Controllers/PostController.php

public function destroy(Request $request, $id)
    {
        $post = BlogPost::findorFail($id);

        // if (Gate::denies('delete-post', $post)) {
        //     abort(403, "You can't edit this blog post!!");
        // }
        $this->authorize('delete-post', $post); // 条件に合わなければ403を返す

```

# 89
Gateのallows()について
denies()の逆であり、
同様にtrue, falseを返す
```
$post = BlogPost::find(17);
[!] Aliasing 'BlogPost' to 'App\BlogPost' for this Tinker session.
=> App\BlogPost {#4040
     id: 17,
     created_at: "2020-10-08 13:44:37",
     updated_at: "2020-10-08 13:44:37",
     title: "Vitae esse iusto autem aut enim placeat aspernatur.",
     content: """
       Tenetur consequatur sint sed molestiae nemo vero occaecati. Ad in natus voluptas quo enim. Molestias adipisci tempora qui. Nobis fuga et reprehenderit et non.
       """,
     user_id: 19,   // IDは19
     deleted_at: null,
   }
>>> $user = User::find(1);
=> App\User {#4039
     id: 1,         // IDは１
     name: "Oliver Sykes",
     email: "olover@laravel.test",
     email_verified_at: "2020-10-08 13:44:36",
     created_at: "2020-10-08 13:44:36",
     updated_at: "2020-10-08 13:44:36",
   }
>>> Gate::forUser($user)->denies('update->post', $post); 
=> true
>>> Gate::forUser($user)->allows('update->post', $post);
=> false
>>>
```