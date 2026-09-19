<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Review;

class ReviewLikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reviewLikes = [
            [
                'email' => 'yamada@example.com',
                'review_ids' => [2,3,4,5,6,8,10],
            ],
            [
                'email' => 'suzuki@example.com',
                'review_ids' => [1,3,6,7,8,9,11],
            ],
            [
                'email' => 'tanaka@example.com',
                'review_ids' => [2,4,5,6,9,11],
            ],
            [
                'email' => 'sato@example.com',
                'review_ids' => [1,2,5,7,9,10],
            ],
            [
                'email' => 'takahashi@example.com',
                'review_ids' => [1,3,4,7,8,10,11],
            ],
        ];

        foreach ($reviewLikes as $data) {
            $user = User::where('email', $data['email'])
            ->first();

            $user->reviewLikes()->syncWithoutDetaching($data['review_ids']);
        }
    }
}
