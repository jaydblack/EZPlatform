<?php

namespace EzSystems\TweetFieldTypeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('EzSystemsTweetFieldTypeBundle:Default:index.html.twig');
    }
}
