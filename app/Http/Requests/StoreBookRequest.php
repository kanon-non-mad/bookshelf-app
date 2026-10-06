<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $book = $this->route('book');

        $isbnRule = Rule::unique('books', 'isbn');

        if ($book) {
            $isbnRule->ignore($book->id);
        }

        return [
            'title' => ['required','string'],
            'author' => ['required','string'],
            'isbn' => [
                'required',
                'digits:13',
                $isbnRule,
            ],
            'published_at' => ['required','date'],
            'description' => ['nullable','string'],
            'book_image' => ['nullable','string','url'],
            'genres' => ['required','array','min:1'],
            'genres.*' => ['integer','exists:genres,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'タイトルは必須です。',
            'author.required' => '著者名は必須です。',
            'isbn.required' => 'ISBNは必須です。',
            'isbn.digits' => 'ISBNは13桁で入力してください。',
            'isbn.unique' => 'このISBNは既に登録されています。',
            'published_at.required' => '出版日は必須です。',
            'published_at.date' => '正しい日付を入力してください。',
            'genres.required' => 'ジャンルを選択してください。',
            'genres.min' => 'ジャンルは1つ以上選択してください。',
            'genres.*.exists' => '存在しないジャンルが指定されています。',
            'genres.array' => 'ジャンルの形式が正しくありません。',
            'genres.*.integer' => 'ジャンルIDは整数で指定してください。',
            'book_image.url' => '画像URLの形式が正しくありません。',
        ];
    }
}
