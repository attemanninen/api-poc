<?php

namespace App\Controller\UI;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ui/users", name="app_ui_user_")
 */
class UserController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $repository;

    public function __construct(
        UserRepository $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * @Route("", name="list")
     */
    public function list(): Response
    {
        $users = $this->repository->findBy([
            'company' => $this->getUser()->getCompany()
        ]);

        return $this->render('user/list.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/{id}", name="show")
     */
    public function show(User $user): Response
    {
        // TODO: Security voter

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

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
