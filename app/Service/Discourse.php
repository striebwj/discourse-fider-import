<?php

namespace App\Service;

use Illuminate\Support\Facades\Http;

class Discourse
{
    private string $baseUrl;

    private string $apiKey;

    private int $categoryId;

    public function __construct()
    {
        $this->baseUrl = config('discourse.base_url');
        $this->apiKey = config('discourse.key');
        $this->categoryId = config('discourse.category_id');
    }

    public function createPost($title, $content)
    {
        $response = Http::withHeader(['Api-Key' => $this->apiKey])
            ->post("{$this->baseUrl}/post", [
                'raw' => $content,
                'title' => $title,
                'category' => $this->categoryId,
            ])
            ->asForm();

        dd($response);
    }

}
