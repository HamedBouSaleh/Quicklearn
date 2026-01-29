<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Exception;

class YouTubeTranscriptService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Extract YouTube video ID from URL
     */
    protected function extractVideoId($url)
    {
        // Handle different YouTube URL formats
        $patterns = [
            '/youtube\.com\/watch\?v=([^&]+)/',
            '/youtu\.be\/([^?]+)/',
            '/youtube\.com\/embed\/([^?]+)/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * Get transcript/caption for a YouTube video
     * Note: This is a simplified version. For production, consider using YouTube Data API
     */
    public function getTranscript($videoUrl)
    {
        $videoId = $this->extractVideoId($videoUrl);
        
        if (!$videoId) {
            throw new Exception('Invalid YouTube URL');
        }

        try {
            // Using YouTube Data API v3 (requires API key)
            $apiKey = config('services.youtube.api_key');
            
            if (empty($apiKey)) {
                // Return basic info if no API key
                return "YouTube Video ID: {$videoId}\nNote: Configure YOUTUBE_API_KEY in .env to fetch video transcripts.";
            }

            // Get video details
            $response = $this->client->get('https://www.googleapis.com/youtube/v3/videos', [
                'query' => [
                    'part' => 'snippet',
                    'id' => $videoId,
                    'key' => $apiKey,
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            if (isset($data['items'][0]['snippet'])) {
                $snippet = $data['items'][0]['snippet'];
                $transcript = "Video Title: {$snippet['title']}\n\n";
                $transcript .= "Description:\n{$snippet['description']}\n\n";
                
                // Note: Getting actual captions requires additional API calls and permissions
                $transcript .= "Note: This is the video description. Actual transcript extraction requires YouTube caption API access.";
                
                return $transcript;
            }

            return "YouTube Video (ID: {$videoId})\nCould not fetch video details.";

        } catch (Exception $e) {
            Log::warning('YouTube API Error: ' . $e->getMessage());
            return "YouTube Video ID: {$videoId}";
        }
    }

    /**
     * Get video metadata without transcript
     */
    public function getVideoMetadata($videoUrl)
    {
        $videoId = $this->extractVideoId($videoUrl);
        
        if (!$videoId) {
            return null;
        }

        return [
            'video_id' => $videoId,
            'url' => $videoUrl,
            'embed_url' => "https://www.youtube.com/embed/{$videoId}"
        ];
    }
}
