<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class SharingRuleController extends AbstractController
{
    /**
     * This is just a tmp controller action to test shared tasks.
     *
     * @Route("/sharing-rules/tasks", name="app_sharing_rule_list_tasks")
     */
    public function listTasks(TaskRepository $repository, SerializerInterface $serializer
    ): Response {
        $tasks = $repository->findAll();

        $context = [AbstractNormalizer::GROUPS => 'public'];
        $tasks = $serializer->normalize($tasks, null, $context);

        return $this->json($tasks);
    }
}
