# Bookshelf App

## 概要

書籍管理アプリです。

ユーザーは書籍の登録・編集・削除、レビュー投稿、お気に入り登録などを行えます。
また、API機能として書籍情報の取得・登録・更新・削除を提供しています。

## 使用技術

- PHP 8.x
- Laravel 10
- MySQL 8.4
- Docker
- Laravel Sail
- Laravel Sanctum

## 環境構築

### リポジトリを取得

```bash
git clone https://github.com/kanon-non-mad/bookshelf-app.git
cd bookshelf-app
```

## 起動方法

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
```

## テスト

```bash
./vendor/bin/sail artisan test
```

## 主な機能

- ユーザー認証
- 書籍CRUD
- レビュー投稿
- お気に入り登録
- いいね機能
- ジャンル管理

## API仕様

書き込み系APIでは Laravel Sanctum の Bearer Token認証を使用しています。

### 認証

書き込み系APIでは Laravel Sanctum を使用した Bearer Token認証を採用しています。

ログインAPIで取得したtokenを使用します。

リクエストヘッダー:

Authorization: Bearer {token}

### 認証API

| Method | URL | 認証 |
|---|---|---|
| POST | /api/v1/auth/login | 不要 |

### 書籍API

| Method | URL                | 認証 |
| ------ | ------------------ | ---- |
| GET    | /api/v1/books      | 不要 |
| GET    | /api/v1/books/{id} | 不要 |
| POST   | /api/v1/books      | 必要 |
| PUT    | /api/v1/books/{id} | 必要 |
| DELETE | /api/v1/books/{id} | 必要 |

### クエリパラメータ

| Parameter | 説明               |
| --------- | ------------------ |
| keyword   | タイトル・著者検索 |
| genre_id  | ジャンル検索       |

## テスト結果

60 tests passed (134 assertions)
