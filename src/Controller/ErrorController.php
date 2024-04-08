<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Throwable;

class ErrorController extends AbstractController
{
    public function showError(Throwable $exception): Response
    {
        return $this->render('bundles/TwigBundle/Exception/error404.html.twig');
    }

}
