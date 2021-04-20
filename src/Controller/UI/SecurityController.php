<?php

namespace App\Controller\UI;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    /**
     * @Route("/ui/logout", name="app_ui_logout", methods={"GET"})
     */
    public function logout()
    {
        // Nothing to do.
    }
}
