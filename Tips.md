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
