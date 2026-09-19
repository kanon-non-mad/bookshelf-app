<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Book;
use App\Models\Genre;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        $books = [
            ['title' => '吾輩は猫である',
            'author' => '夏目漱石',
            'isbn' => '9784101010014',
            'published_at' => '1905-01-01',
            'description' => '夏目漱石の代表作。',
            'book_image' => 'https://placehold.co/200x300/e2e8f0/475569?text=1',
            'genres' => ['小説'],
        ],
        [
            'title' => '人を動かす',
            'author' => 'D・カーネギー',
            'isbn' => '9784422100524',
            'published_at' => '1936-10-01',
            'description' => 'あらゆる自己啓発本の原点となった不朽の名著。',
            'book_image' => 'https://placehold.co/200x300/e2e8f0/475569?text=2',
            'genres' => ['ビジネス','自己啓発'],
        ],
        [
            'title' => 'リーダブルコード',
            'author' => 'Dustin Boswell',
            'isbn' => '9784873115658',
            'published_at' => '2012-06-23',
            'description' => 'より良いコードを書くためのシンプルで実践的なテクニック。',
            'book_image' => 'https://placehold.co/200x300/e2e8f0/475569?text=3',
            'genres' => ['技術書'],
        ],
        [
            'title' => '７つの習慣',
            'author' => 'スティーブン・R・コヴィー',
            'isbn' => '9784863940246',
            'published_at' => '2013-08-30',
            'description' => '人生で成功するための考え方が７つ紹介。',
            'book_image' => 'https://placehold.co/200x300/e2e8f0/475569?text=4',
            'genres' => ['ビジネス','自己啓発'],
        ],
        [
            'title' => '坊っちゃん',
            'author' => '夏目漱石',
            'isbn' => '9784101010021',
            'published_at' => '1906-04-01',
            'description' => '夏目漱石による青春小説。',
            'book_image' => 'https://placehold.co/200x300/e2e8f0/475569?text=5',
            'genres' => ['小説'],
        ],
        [
            'title' => 'サピエンス全史',
            'author' => 'ユヴァル・ノア・ハラリ',
            'isbn' => '9784309226712',
            'published_at' => '2016-09-08',
            'description' => 'ホモサピエンスの石器時代から２１世紀までの人類の歴史を概観するものである。',
            'book_image' => 'https://placehold.co/200x300/e2e8f0/475569?text=6',
            'genres' => ['歴史','科学'],
        ],
        [
            'title' => 'Clean Code',
            'author' => 'Robert.C.Martin',
            'isbn' => '9784048930598',
            'published_at' => '2017-12-18',
            'description' => 'コードを書き、読み、洗練する。プロのプログラマになるには、洗練されたコード(クリーンコード)を書くことが必須と言える。',
            'book_image' => 'https://placehold.co/200x300/e2e8f0/475569?text=7',
            'genres' => ['技術書'],
        ],
        [
            'title' => '嫌われる勇気',
            'author' => '岸見一郎・古賀史健',
            'isbn' => '9784478025819',
            'published_at' => '2013-12-13',
            'description' => 'アドラー心理学を対話形式で分かりやすく学べる一冊。',
            'book_image' => 'https://placehold.co/200x300/e2e8f0/475569?text=8',
            'genres' => ['自己啓発'],
        ],
        [
            'title' => '火花',
            'author' => '又吉直樹',
            'isbn' => '9784163902302',
            'published_at' => '2015-03-11',
            'description' => '笑いとは何か、人間とは何かを描ききった又吉のデビュー小説。',
            'book_image' => 'https://placehold.co/200x300/e2e8f0/475569?text=9',
            'genres' => ['小説'],
        ],
        [
            'title' => 'FACTFULNESS',
            'author' => 'ハンス・ロスリング',
            'isbn' => '9784822289607',
            'published_at' => '2019-01-11',
            'description' => 'データや事実にもとづき、世界を読み解く習慣。賢い人ほどとらわれる10の思い込みから解放されれば、癒され、世界を正しく見るスキルが身につく。',
            'book_image' => 'https://placehold.co/200x300/e2e8f0/475569?text=10',
            'genres' => ['ビジネス','科学'],
        ],
        [
            'title' => 'コンテナ物語',
            'author' => 'マルク・レビンソン',
            'isbn' => '9784822251468',
            'published_at' => '2007-01-18',
            'description' => '一人の実業家の発想から始まった物流革命の記録。',
            'book_image' => 'https://placehold.co/200x300/e2e8f0/475569?text=11',
            'genres' => ['ビジネス','歴史'],
        ],
    ];

    foreach ($books as $data) {

        $book = Book::firstOrCreate(
            [
            'isbn' => $data['isbn'],
            ],
            [
                'user_id' => $users->random()->id,
                'title' => $data['title'],
                'author' => $data['author'],
                'published_at' => $data['published_at'],
                'description' => $data['description'],
                'book_image' => $data['book_image'],
            ]
        );

        $genreIds = Genre::whereIn('name', $data['genres'])->pluck('id');

        $book->genres()->sync($genreIds);

        }
    }
}
