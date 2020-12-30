<?php

namespace App\Service;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Discourse
{
    private string $baseUrl;

    private string $apiKey;

    private string $username;

    private int $categoryId;

    public function __construct()
    {
        $this->baseUrl = config('discourse.base_url');
        $this->apiKey = config('discourse.key');
        $this->username = config('discourse.username');
        $this->categoryId = config('discourse.category_id');
    }

    public function createPost($postData)
    {
        $response = Http::withHeaders([
            'Api-Key' => $this->apiKey,
            'Api-Username' => $this->username,
        ])
            ->asForm()
            ->post("{$this->baseUrl}/posts", [
                'raw' => $postData['content'],
                'title' => $postData['title'],
                'category' => $this->categoryId,
            ]);

        if ($response->failed()) {
            Log::error($response->throw());
            return false;
        }

        return true;
    }

}
