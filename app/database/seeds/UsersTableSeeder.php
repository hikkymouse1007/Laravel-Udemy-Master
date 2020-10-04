<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $usersCount = max((int)$this->command->ask('How many users would you like?', 20), 1); //最低1以上のユーザを作成
        factory(App\User::class)->state('oliver')->create(); //stateメゾッド
        factory(App\User::class, $usersCount)->create(); // askで指定した数のユーザの追加
    }
}
