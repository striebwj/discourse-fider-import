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
        $content .= $this->getPostComments($postId);

        return [
            'title' => $title,
            'content' => $content
        ];
    }

    private function getPostDetails($postId)
    {
        $response = Http::withToken($this->apiKey)
                        ->get("{$this->baseUrl}/api/v1/posts/{$postId}");

        if ($response->failed()) {
            return null;
        }

        $json = $response->json();

        $content = "\n\n --- \n\n**Author**: {$json['user']['name']}";

        $content .= "\n\n**Vote count**: {$json['votesCount']}";

        $content .= "\n\n**Status**: {$json['status']}";

        // Had a response from an admin
        if (isset($json['response'])) {
            $content .= "\n\n[quote]\n {$json['response']['text']} \n\n ~ posted by {$json['response']['user']['name']} \n[/quote]\n";
        }

        // It has tags
        if (count($json['tags']) > 0) {
            $content .= "\n\n**Tags**: \n\n";
            // Loop over tags
            foreach ($json['tags'] as $tag) {
                $content .= "* {$tag} \n";
            }
        }

        return $content;
    }

    private function getPostComments($postId)
    {
        $response = Http::withToken($this->apiKey)
            ->get("{$this->baseUrl}/api/v1/posts/{$postId}/comments");

        if ($response->failed()) {
            return null;
        }

        $comments = $response->json();

        $content = "\n\n --- \n\n";

        $content .= "\n\n **Comments** \n\n";

        // It has comments
        if (count($comments) > 0) {
            // Loop over the comments and add them to the base raw
            foreach ($comments as $comment) {

                $author = $comment['user']['name'];

                if (!$author) {
                    // Check for Anonymous comments
                    $author = 'a ClassicPress Supporter';
                }

                $content .= "[quote]\n {$comment['content']} \n\n ~ posted by {$author} \n[/quote]\n";
            }

            return $content;
        }

        return "\n\n No comments posted.";
    }


}
