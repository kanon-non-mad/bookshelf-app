<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Book;
use App\Models\Review;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reviews = [
            [
            'email' => 'suzuki@example.com',
            'isbn' => '9784101010014',
            'rating' => 4.0,
            'content' => '久しぶりに読みました。おすすめです。',
            ],
            [
            'email' => 'tanaka@example.com',
            'isbn' => '9784101010014',
            'rating' => 5.0,
            'content' => '久しぶりに読むと昔とはまた違う視点で読めたと思います。',
            ],
            [
            'email' => 'sato@example.com',
            'isbn' => '9784101010014',
            'rating' => 3.0,
            'content' => '文字が小さくて読みずらかった。',
            ],
            [
            'email' => 'suzuki@example.com',
            'isbn' => '9784422100524',
            'rating' => 4.0,
            'content' => '気持ちよく人を動かすのに必要なポイントがテーマ毎に書かれた本。',
            ],
            [
            'email' => 'tanaka@example.com',
            'isbn' => '9784422100524',
            'rating' => 5.0,
            'content' => '人との関わり方を学ぶことができます。',
            ],
            [
            'email' => 'yamada@example.com',
            'isbn' => '9784873115658',
            'rating' => 5.0,
            'content' => 'エンジニアの共通言語、共通の話題としておすすめ。',
            ],
            [
            'email' => 'suzuki@example.com',
            'isbn' => '9784873115658',
            'rating' => 3.0,
            'content' => 'プログラミング初心者向けの内容。',
            ],
            [
            'email' => 'sato@example.com',
            'isbn' => '9784873115658',
            'rating' => 4.0,
            'content' => '挿絵もユーモアもあって初学者でも楽しめる。',
            ],
            [
            'email' => 'yamada@example.com',
            'isbn' => '9784863940246',
            'rating' => 5.0,
            'content' => '人生や仕事の軸をしっかり持ちたい人に、ぜひ手に取ってほしい一冊。',
            ],
            [
            'email' => 'sato@example.com',
            'isbn' => '9784863940246',
            'rating' => 4.0,
            'content' => '豊富な実例。「善き人であれ」。人生の伴侶として手元に置いておきたい一冊。',
            ],
            [
            'email' => 'takahashi@example.com',
            'isbn' => '9784863940246',
            'rating' => 3.0,
            'content' => '分厚くて最後まで読む自信がない。',
            ],
            [
            'email' => 'yamada@example.com',
            'isbn' => '9784101010021',
            'rating' => 5.0,
            'content' => '夏目漱石の本は外れがない。また読もうと思える作品。',
            ],
            [
            'email' => 'tanaka@example.com',
            'isbn' => '9784101010021',
            'rating' => 4.0,
            'content' => '読解力がつく本としておすすめ。',
            ],
            [
            'email' => 'sato@example.com',
            'isbn' => '9784101010021',
            'rating' => 5.0,
            'content' => '次が気になってあっという間に読み終えてしまった。',
            ],
            [
            'email' => 'yamada@example.com',
            'isbn' => '9784309226712',
            'rating' => 5.0,
            'content' => '自分のモヤモヤが少し晴れた気がする。',
            ],
            [
            'email' => 'tanaka@example.com',
            'isbn' => '9784309226712',
            'rating' => 3.0,
            'content' => 'あら捜し目的で読むのなら楽しめるかも。',
            ],
            [
            'email' => 'takahashi@example.com',
            'isbn' => '9784309226712',
            'rating' => 5.0,
            'content' => '読めば読むほどおもろい。',
            ],
            [
            'email' => 'yamada@example.com',
            'isbn' => '9784048930598',
            'rating' => 4.0,
            'content' => 'コードが読みづらい。',
            ],
            [
            'email' => 'sato@example.com',
            'isbn' => '9784048930598',
            'rating' => 4.0,
            'content' => '他の本と合わせて読むべき。',
            ],
            [
            'email' => 'yamada@example.com',
            'isbn' => '9784478025819',
            'rating' => 5.0,
            'content' => '読みやすく、面白く、一気に読んだ。',
            ],
            [
            'email' => 'suzuki@example.com',
            'isbn' => '9784478025819',
            'rating' => 4.0,
            'content' => '人生ってシンプル！',
            ],
            [
            'email' => 'tanaka@example.com',
            'isbn' => '9784478025819',
            'rating' => 5.0,
            'content' => '人間関係に悩んでいればおすすめ。',
            ],
            [
            'email' => 'yamada@example.com',
            'isbn' => '9784163902302',
            'rating' => 5.0,
            'content' => '笑いあり涙ありで面白かった。',
            ],
            [
            'email' => 'tanaka@example.com',
            'isbn' => '9784163902302',
            'rating' => 5.0,
            'content' => '登場人物の関係が秀逸でした。',
            ],
            [
            'email' => 'sato@example.com',
            'isbn' => '9784163902302',
            'rating' => 3.0,
            'content' => '山も谷も落ちも弱く、読者を面白さで引っ張るという力は感じられなかった。',
            ],
            [
            'email' => 'takahashi@example.com',
            'isbn' => '9784163902302',
            'rating' => 3.0,
            'content' => '文章はうまい。芥川賞受賞作としては面白いほう。',
            ],
            [
            'email' => 'tanaka@example.com',
            'isbn' => '9784822289607',
            'rating' => 5.0,
            'content' => '自分の知識は何をもとにするのかを改め考えることができる本。',
            ],
            [
            'email' => 'sato@example.com',
            'isbn' => '9784822289607',
            'rating' => 5.0,
            'content' => '「世界は思ったよりもずっと良くなっている」その事実に気づかせてくれる一冊。',
            ],
            [
            'email' => 'takahashi@example.com',
            'isbn' => '9784822289607',
            'rating' => 5.0,
            'content' => '読書が苦手な僕でも、無理してでも読むべき本だと感じた。なんでも知りたいと好奇心がある子には読んで欲しい一冊。',
            ],
            [
            'email' => 'tanaka@example.com',
            'isbn' => '9784822251468',
            'rating' => 4.0,
            'content' => '見聞を広める意味では良い本ですが、最後までかぶりつきそうな興味は惹かれませんでした‥。',
            ],
            [
            'email' => 'sato@example.com',
            'isbn' => '9784822251468',
            'rating' => 4.0,
            'content' => '面白いけど後半は冗長に感じる。',
            ],
            [
            'email' => 'takahashi@example.com',
            'isbn' => '9784822251468',
            'rating' => 4.0,
            'content' => '流通革命を通じて、未だ資本主義の可能性を信じることができた。',
            ],
        ];

        foreach ($reviews as $data) {
            $user = User::where('email', $data['email'])
            ->first();

            $book = Book::where('isbn', $data['isbn'])
            ->first();

            Review::create([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'rating' => $data['rating'],
                'content' => $data['content'],
            ]);
        }
    }
}
