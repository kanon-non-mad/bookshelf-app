<?php

namespace Tests\Feature\Api\V1;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use App\Models\Genre;
use App\Models\Review;
use Laravel\Sanctum\Sanctum;

class BookApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_APIで書籍一覧を取得できる(): void
    {
        Book::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/books');

        $response->assertStatus(200);

        $response->assertJsonCount(3);
    }

    public function test_APIで書籍の詳細を取得できる(): void 
    {
        $book = Book::factory()->create();

        $response = $this->getJson("/api/v1/books/{$book->id}");

        $response->assertStatus(200);

        $response->assertJson([
            'data' => [
                'title' => $book->title,
            ]
        ]);
    }

    public function test_APIでログインユーザーが書籍登録できる(): void {
        $user = User::factory()->create();

        $genre = Genre::create([
            'name' => '技術書',
        ]);

        Sanctum::actingAs($user);

        $data = [
            'isbn' => '1234567890123',
            'title' => 'APIテスト本',
            'author' => 'API著者',
            'published_at' => '2026-01-01',
            'description' => 'API用',
            'genres' => [$genre->id],
        ];

        $response = $this->postJson('/api/v1/books', $data);

        $response->assertStatus(201);

        $response->assertJson([
            'message' => 'Book created successfully.',
            'data' => [
                'title' => 'APIテスト本',
            ]
        ]);

        $this->assertDatabaseHas('books',[
            'title' => 'APIテスト本',
            'isbn' => '1234567890123',
            'user_id' => $user->id,
        ]);
    }

    public function test_APIでログインユーザーが書籍更新できる(): void {
        $user = User::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

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

        Sanctum::actingAs($user);

        $response = $this->patchJson("/api/v1/books/{$book->id}", $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('books',[
            'title' => 'APIテスト本',
            'isbn' => '1234567890123',
            'id' => $book->id,
        ]);
    }

    public function test_APIでログインユーザーが書籍削除できる(): void {
        $user = User::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/v1/books/{$book->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('books',[
            'id' => $book->id,
        ]);
    }

    public function test_APIで他人の書籍を更新できない(): void 
    {
        $owner = User::factory()->create();

        $genre = Genre::create([
            'name' => '技術書',
        ]);

        $book = Book::factory()->create([
            'user_id' => $owner->id,
        ]);

        $data = [
            'isbn' => '1234567890123',
            'title' => 'APIテスト本',
            'author' => 'API著者',
            'published_at' => '2026-01-01',
            'description' => 'API用',
            'genres' => [$genre->id],
        ];

        $otherUser = User::factory()->create();

        Sanctum::actingAs($otherUser);

        $response = $this->patchJson("/api/v1/books/{$book->id}",$data);

        $response->assertStatus(403);
    }

    public function test_APIで他人の書籍を削除できない(): void 
    {
        $owner = User::factory()->create();

        $book = Book::factory()->create([
            'user_id' => $owner->id,
        ]);

        $otherUser = User::factory()->create();

        Sanctum::actingAs($otherUser);

        $response = $this->deleteJson("/api/v1/books/{$book->id}");

        $response->assertStatus(403);
    }

    public function test_APIでタイトル検索ができる(): void 
    {
        $laravelBook = Book::factory()->create([
            'title' => 'Laravel入門',
        ]);

        $phpBook = Book::factory()->create([
            'title' => 'PHP入門',
        ]);

        $javaBook = Book::factory()->create([
            'title' => 'Java入門',
        ]);
        
        $response = $this->getJson("/api/v1/books?keyword=Laravel");

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'title' => 'Laravel入門',
        ]);

        $response->assertJsonMissing([
            'title' => 'PHP入門',
        ]);

        $response->assertJsonMissing([
            'title' => 'Java入門',
        ]);
    }

    public function test_APIで書籍一覧にジャンル情報が含まれる(): void {
        $genre = Genre::factory()->create([
            'name' => 'Laravel'
        ]);

        $book = Book::factory()->create([
            'title' => 'Laravel入門',
        ]);

        $book->genres()->attach($genre);

        $response = $this->getJson('/api/v1/books');

        $response->assertStatus(200);

        $response->assertJson([
            'data' => [
                [
                    'title' => 'Laravel入門',
                    'genres' => [
                        [
                        'name' => 'Laravel',
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function test_APIで書籍一覧にレビュー件数と平均評価が含まれる(): void {
        $user = User::factory()->create();

        $book = Book::factory()->create([
            'title' => 'Laravel入門',
        ]);

        $book->reviews()->create([
            'user_id' => $user->id,
            'content' => '良い本でした',
            'rating' => 5,
        ]);

        $book->reviews()->create([
            'user_id' => $user->id,
            'content' => '勉強になりました',
            'rating' => 3,
        ]);

        $response = $this->getJson('/api/v1/books');

        $response->assertStatus(200);

        $response->assertJson([
            'data' => [
                [
                    'title' => 'Laravel入門',
                    'reviews_count' => 2,
                    'average_rating' => 4,
                ],
            ],
        ]);
    }

    public function test_APIで書籍一覧がページネーションされる(): void {
        Book::factory()->count(11)->create();

        $response = $this->getJson('/api/v1/books');

        $response->assertStatus(200);

        $response->assertJsonCount(10,'data');

        $response->assertJson([
            'meta' => [
                'total' => 11,
            ],
        ]);
    }

    public function test_APIでジャンル検索ができる(): void 
    {
        $laravelGenre = Genre::factory()->create([
            'name' => 'Laravel',
        ]);

        $phpGenre = Genre::factory()->create([
            'name' => 'PHP',
        ]);

        $laravelBook = Book::factory()->create([
            'title' => 'Laravel入門',
        ]);

        $laravelBook->genres()->attach($laravelGenre);


        $phpBook = Book::factory()->create([
            'title' => 'PHP入門',
        ]);

        $phpBook->genres()->attach($phpGenre);

        $response = $this->getJson("/api/v1/books?genre_id={$laravelGenre->id}");

        $response->assertStatus(200);

        $response->assertJson([
            'data' => [
                [
                'title' => 'Laravel入門',
                ],
            ],
        ]);

        $response->assertJsonMissing([
            'title' => 'PHP入門',
        ]);
    }

    public function test_存在しないIDが指定されたとき404になるか(): void {
        $response = $this->getJson('/api/v1/books/999999');

        $response->assertStatus(404);
    }

    public function test_APIで書籍登録時のバリデーションエラーを返す(): void 
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $genre = Genre::factory()->create();

        $data = Book::factory()->make()->only([
            'title',
            'author',
            'published_at',
            'description',
        ]);

        $data['genres'] = [$genre->id];

        unset($data['isbn']);

        $response = $this->postJson('/api/v1/books', $data);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors([
            'isbn'
        ]);

        $response->assertJsonFragment([
            'ISBNは必須です。',
        ]);
    }

    public function test_APIで存在しないIDを更新すると404になる(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->putJson('/api/v1/books/999999');

        $response->assertStatus(404);
    }

    public function test_APIで書籍削除すると関連データも削除される(): void 
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
        ]);

        $review->likes()->create([
            'user_id' => $user->id,
        ]);

        $book->favorites()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->deleteJson("/api/v1/books/{$book->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);

        $this->assertDatabaseMissing('reviews', [
            'book_id' => $book->id,
        ]);

        $this->assertDatabaseMissing('likes', [
            'review_id' => $review->id,
        ]);

        $this->assertDatabaseMissing('favorites', [
            'book_id' => $book->id,
        ]);
    }
}
