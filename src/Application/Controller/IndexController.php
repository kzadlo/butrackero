<?php

namespace App\Application\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /** @Route("/", name="app_index") */
    public function indexAction()
    {
        return $this->render('homepage.html.twig');
    }
}
