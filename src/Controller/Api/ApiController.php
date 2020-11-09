<?php

namespace App\Controller\Api;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;

abstract class ApiController extends AbstractFOSRestController
{
    protected function badRequest($violations): View
    {
        return $this->view(
            [
                'violations' => $violations,
            ],
            Response::HTTP_BAD_REQUEST
        );
    }
}
