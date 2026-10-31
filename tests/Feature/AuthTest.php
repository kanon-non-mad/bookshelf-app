<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_未ログインユーザーが保護ページにアクセスできない(): void
    {
        $response = $this->get('/books/create');

        $response->assertStatus(302);

        $response->assertRedirect('/login');
    }

    public function test_ログインユーザーがログイン必須ページにアクセスできる(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/books/create');

        $response->assertStatus(200);
    }

    public function test_ゲストユーザーがログイン済みページに入れない(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/login');

        $response->assertStatus(302);

        $response->assertRedirect('/books');
    }
}
