<?php


namespace App\Commands;

use App\Service\Discourse;
use App\Service\Fider;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;

class ImportFiderToDiscourse extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'import';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Import posts from Fider to Discourse';


    /**
     * Throttle Variables
     */
    private $maxRequestsPerMinute = 20; // 20 is the default Discourse rate limit
    private $requestCount = 0;
    private $firstRequestTime = null;

    /**
     * Error Tracking
     */
    private $postId;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Get the file path from the user.
        $path = $this->ask('What is the path to your Fider posts export file?');


        // Check it exists and we can read it.
        if(! File::exists($path) || ! File::isReadable($path)) {
            $this->error('We cannot find the file or it is not readable');
            return false;
        }

        // Get the file
        $file = File::get($path);

        // Convert the files json to a collection
        $array = json_decode($file, true);
        $posts = collect($array);

        // Check it matches the structure we expect
        if (! isset($posts->first()['title']) && ! isset($posts->first()['description']) ) {
            $this->error('We cannot seem to parse your file, is it a post export from fider?');
            return false;
        }

        // Warn about time expectation with Discourse Rate Limiting
        $count = $posts->count();
        $minutes = ceil($count/60);
        $this->warn("With {$count} Fider posts to be imported it may take up to {$minutes} minutes to finish the import due to the API rate limit by Discourse.");

        // Loop over the collection and create Discourse threads for each post
        $this->withProgressBar($posts, function ($post) {
            $this->postId = $post['id'];
            // Actually create the Discourse Post
            $fiderData = $this->getFiderPost($post);
            $this->createDiscoursePost($fiderData);
        });

        // Let the user know we finished
        $this->newLine();
        $this->info('Finished importing the posts from Fider to Discourse.');
    }

    private function getFiderPost($post)
    {
        $fider = new Fider();

        return $fider->getPostData($post);
    }

    private function createDiscoursePost($postData)
    {
        // Discourse has a rate limit of 20 API requests
        // per minute so we will throttle the creates
        if ($this->requestCount >= $this->maxRequestsPerMinute) {
            sleep(60);
            $this->firstRequestTime = null;
            $this->requestCount = 0;
        }

        if ($this->firstRequestTime === null) {
            $this->firstRequestTime = microtime(true);
        }

        $this->requestCount++;

        $successful = app(Discourse::class)->createPost($postData);

        if (!$successful) {
            $this->warn("Failed to create post id {$this->postId}");
        }
    }
}
