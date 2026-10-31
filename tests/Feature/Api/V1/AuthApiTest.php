<?php

namespace Tests\Feature\Api\V1;

use App\Models\Genre;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_ap_iでログインに成功できる(): void
    {
        $user = User::factory()->create();

        $data = [
            'email' => $user->email,
            'password' => 'password',
        ];

        $response = $this->postJson('/api/v1/auth/login', $data);

        $response->assertStatus(200);
    }

    public function test_ログイン成功時にtokenが返る(): void
    {
        $user = User::factory()->create();

        $data = [
            'email' => $user->email,
            'password' => 'password',
        ];

        $response = $this->postJson('/api/v1/auth/login', $data);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'token',
        ]);
    }

    public function test_間違ったパスワードではログインできない(): void
    {
        $user = User::factory()->create();

        $data = [
            'email' => $user->email,
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/v1/auth/login', $data);

        $response->assertStatus(401);

        $response->assertJson([
            'message' => '認証情報が正しくありません。',
        ]);
    }

    public function test_取得したtokenで認証が必要な_ap_iにアクセスできる(): void
    {
        $user = User::factory()->create();

        $data = [
            'email' => $user->email,
            'password' => 'password',
        ];

        $loginResponse = $this->postJson('/api/v1/auth/login', $data);

        $token = $loginResponse->json('token');

        $genre = Genre::create([
            'name' => '技術書',
        ]);

        $bookData = [
            'isbn' => '1234567890123',
            'title' => 'API認証テスト本',
            'author' => 'API著者',
            'published_at' => '2026-01-01',
            'description' => 'tokenテスト',
            'genres' => [$genre->id],
        ];

        $response = $this->withHeader(
            'Authorization', 'Bearer '.$token)
            ->postJson('/api/v1/books', $bookData);

        $response->assertStatus(201);
    }

    public function test_tokenなしでは書籍登録できない(): void
    {
        $genre = Genre::create([
            'name' => '技術書',
        ]);

        $data = [
            'isbn' => '1234567890123',
            'title' => 'APIテスト本',
            'author' => 'API著者',
            'published_at' => '2026-01-01',
            'description' => 'API用',
            'genres' => [$genre->id],
        ];

        $response = $this->postJson('/api/v1/books', $data);

        $response->assertStatus(401);
    }

    public function test_不正なtokenでは書籍登録できない(): void
    {
        $genre = Genre::create([
            'name' => '技術書',
        ]);

        $data = [
            'isbn' => '1234567890123',
            'title' => 'APIテスト本',
            'author' => 'API著者',
            'published_at' => '2026-01-01',
            'description' => 'API用',
            'genres' => [$genre->id],
        ];

        $response = $this
            ->withHeader('Authorization', 'Bearer fake-token')
            ->postJson('/api/v1/books', $data);

        $response->assertStatus(401);
    }
}
