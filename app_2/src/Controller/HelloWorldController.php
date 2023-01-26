<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloWorldController
{
    #[Route('/lucky/number/{max}', name: 'app_lucky_number')]
    public function helloWorld(): Response
    {
        $text = 'Hello World!';

        return new Response(
            '<html><body>'.$text.'</body></html>'
        );
    }
}