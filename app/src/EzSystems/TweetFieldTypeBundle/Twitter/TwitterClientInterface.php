<?php

namespace EzSystems\TweetFieldTypeBundle\Twitter;

interface TwitterClientInterface
{
    public function getEmbed($statusUrl);

    public function getAuthor($statusUrl);
}