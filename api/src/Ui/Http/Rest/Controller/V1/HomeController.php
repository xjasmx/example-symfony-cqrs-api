<?php

declare(strict_types=1);

namespace App\Ui\Http\Rest\Controller\V1;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->json(
            [
                'name' => 'JSON API',
                'version' => '1.0',
            ]
        );
    }
}
