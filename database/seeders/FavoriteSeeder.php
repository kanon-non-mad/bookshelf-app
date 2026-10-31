<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $favorites = [
            [
                'email' => 'yamada@example.com',
                'isbns' => [
                    '9784101010014',
                    '9784422100524',
                    '9784873115658',
                ],
            ],
            [
                'email' => 'suzuki@example.com',
                'isbns' => [
                    '9784101010014',
                    '9784863940246',
                    '9784163902302',
                    '9784822289607',
                ],
            ],
            [
                'email' => 'tanaka@example.com',
                'isbns' => [
                    '9784101010014',
                    '9784163902302',
                    '9784422100524',
                ],
            ],
            [
                'email' => 'sato@example.com',
                'isbns' => [
                    '9784873115658',
                    '9784309226712',
                    '9784163902302',
                    '9784822251468',
                ],
            ],
            [
                'email' => 'takahashi@example.com',
                'isbns' => [
                    '9784163902302',
                    '9784822289607',
                    '9784822251468',
                ],
            ],
        ];

        foreach ($favorites as $data) {
            $user = User::where('email', $data['email'])
                ->first();

            $bookIds = Book::whereIn('isbn', $data['isbns'])
                ->pluck('id');

            $user->favoriteBooks()->syncWithoutDetaching($bookIds);
        }
    }
}
