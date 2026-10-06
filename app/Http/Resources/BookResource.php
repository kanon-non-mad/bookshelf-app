<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'author' => $this->author,
            'image_url' => $this->book_image
                ? asset('storage/' .$this->book_image)
                : null,
            'reviews_count' =>$this->reviews_count,
            'average_rating' => $this->reviews_avg_rating,
            'genres' => GenreResource::collection(
                $this->whenLoaded('genres')
            ),
        ];
    }
}
