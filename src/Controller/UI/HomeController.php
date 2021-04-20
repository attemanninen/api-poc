<?php

namespace App\Controller\UI;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/ui", name="app_ui_home_index")
     */
    public function index(): Response
    {
        return $this->redirectToRoute('app_ui_task_list');
    }
}
