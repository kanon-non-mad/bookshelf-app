<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
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
            'content' => ['nullable','string'],
            'rating' => ['required','between:1,5','numeric'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.string' => 'レビュー内容は文字列で入力してください。',
            'rating.required' => '評価を入力してください。',
            'rating.between' => '評価は1から5の範囲で入力してください。',
            'rating.numeric' => '評価は数値で入力してください。',
        ];
    }
}
