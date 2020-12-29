<?php


namespace App\Service;


use Illuminate\Support\Facades\Http;

class Fider
{
    private string $baseUrl;

    private string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('fider.base_url');
        $this->apiKey = config('fider.key');
    }

    public function getPostData($post)
    {
        // Set the default data we require
        $content = $post["description"];
        $title = $post["title"];

        // Track the post ID
        $postId = $post['number'];

        $content .= $this->getPostDetails($postId);
        $content .= $this->getPostComments(51);

        dd($content);

        return [
            'title' => $title,
            'content' => $content
        ];
    }

    private function getPostDetails($postId)
    {
        $details = Http::withToken($this->apiKey)
                        ->get("{$this->baseUrl}/api/v1/posts/{$postId}");

        if ($details->failed()) {
            return null;
        }

        $json = $details->json();

        return "\n\n This petition was posted by {$json['user']['name']}.\n\nIt had {$json['votesCount']} votes.";
    }

    private function getPostComments($postId)
    {
        $comments = Http::withToken($this->apiKey)
            ->get("{$this->baseUrl}/api/v1/posts/{$postId}/comments");

        if ($comments->failed()) {
            return null;
        }

        $json = $comments->json();


        // Loop over the comments and add them to the base raw

        dd($json);
    }


}
