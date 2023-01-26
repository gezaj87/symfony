<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PrintController
{
    public function printText(string $text): Response
    {
        return new Response(
            '<html><body>'.$text.'</body></html>'
        );
    }
}