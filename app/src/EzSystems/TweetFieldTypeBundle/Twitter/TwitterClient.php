<?php

namespace EzSystems\TweetFieldTypeBundle\Twitter;

class TwitterClient implements TwitterClientInterface
{
    public function getEmbed($statusUrl)
    {
        $parts = explode('/', $statusUrl);

        if (isset($parts[5])) {
            $response = file_get_contents(
                sprintf(
                    'https://api.twitter.com/1/statuses/oembed.json?id=%s&align=center',
                    $parts[5]
                )
            );


            $data = json_decode($response, true);
            return $data['html'];
        }

        return '';
    }

    public function getAuthor($statusUrl)
    {
        return substr(
            $statusUrl,
            0,
            strpos($statusUrl, '/status/')
        );
    }
}