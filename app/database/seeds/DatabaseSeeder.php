<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        if ($this->command->confirm('Do you want to refresh the database?', true)) {
            $this->command->call('migrate:refresh');
            $this->command->info('Database was refreshed');
        }

        Cache::tags(['blog-post'])->flush();
        $this->call([
            UsersTableSeeder::class,
            BlogPostsTableSeeder::class,
            CommentsTableSeeder::class,
        ]);
    }
}
