<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloWorldController
{
    public function helloWorld(): Response
    {
        $text = 'Hello World!';

        return new Response(
            '<html><body>'.$text.'</body></html>'
        );
    }
}