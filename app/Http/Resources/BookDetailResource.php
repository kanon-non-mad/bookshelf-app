<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookDetailResource extends BookResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return array_merge(
            parent::toArray($request),
            [
                'published_at' => $this->published_at,
                'description' => $this->description,
                'reviews' => ReviewResource::collection(
                    $this->whenLoaded('reviews')
                ),
            ]
        );
    }
}
