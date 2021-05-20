<?php

namespace App\Controller\UI;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ui/user", name="app_ui_user_")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/me", name="me")
     */
    public function me(): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
