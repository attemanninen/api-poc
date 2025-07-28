<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/me", name="app_user_me")
     */
    public function me(
        Security $security,
        SerializerInterface $serializer,
    ): Response {
        $user = $security->getUser();

        $context = [AbstractNormalizer::GROUPS => 'public'];
        $user = $serializer->normalize($user, null, $context);

        return $this->json($user);
    }
}
