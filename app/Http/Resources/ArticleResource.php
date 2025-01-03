<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'doc_id' => $this->doc_id,
            'author' => $this->author,
            'category' => $this->category,
            'published_at' => $this->published_at,
            'source' => $this->source,
            'content' => json_decode($this->content),
        ];
    }
}
