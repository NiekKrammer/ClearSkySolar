<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response; // Fix the namespace for Response
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Throwable;

class ErrorController extends AbstractController
{
    public function showError(Throwable $exception): Response
    {
        // Handle different types of errors here and return the appropriate error page
        // For example, you can render different error templates based on the exception
        // You can also log the exception or perform other actions as needed

        return $this->render('bundles/TwigBundle/Exception/error404.html.twig', [], null);
    }

}
