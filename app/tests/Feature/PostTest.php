<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\BlogPost;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function testNoBlogPostWhenNothingInDatabase()
    {
        $response = $this->get('/posts');

        $response->assertSeeText('No blog posts yet!');
    }

    public function testSee1BlogPostWhenThereIs1()
    {
       $post = new BlogPost();
       $post->title = 'New title';
       $post->content = 'Content of the blog post';
       $post->save();

       $response = $this->get('/posts');

       $response->assertSeeText('New title');

       $this->assertDatabaseHas('blog_posts', [
            'title' => 'New title'
       ]);

    }

    public function testStoreValid()
    {
        $params = [
            'title' => 'valid title',
            'content' => 'At least 10 characters'
        ];

        $this->post('/posts', $params)
            ->assertStatus(302)
            ->assertSessionHas('status');

        $this->assertEquals(session('status'), 'Blog post was created!');
    }
}
