<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'title' => ['required','string'],
            'author'=> ['required','string'],
            'isbn' => ['required','digits:13','unique'],
            'published_at' => ['required','date'],
            'description' => ['required','string'],
            'book_image' => ['nullable','string','url'],
            'genres' => ['required','array','min:1'],
            'genres.*' => ['integer','exists:genres,id'],
        ];
    }
}
