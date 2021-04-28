<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Task;
use App\Exception\FormValidationException;
use App\Form\ListParametersType;
use App\Repository\TaskRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class TaskController extends AbstractController
{
    /**
     * @var TaskRepository
     */
    private $repository;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        TaskRepository $repository,
        SerializerInterface $serializer
    ) {
        $this->repository = $repository;
        $this->serializer = $serializer;
    }

    /**
     * @todo groups in filters parameter
     *
     * @Route({"/tasks", "/groups/{id}/tasks"}, name="app_task_list")
     */
    public function list(Request $request, Group $group = null): Response
    {
        $criteria = Criteria::create();
        $form = $this->createForm(ListParametersType::class, $criteria, [
            'model' => Task::class,
        ]);
        $form->submit($request->query->all());

        if (!$form->isValid()) {
            throw new FormValidationException($form);
        }

        if ($group) {
            $this->denyAccessUnlessGranted('view', $group);
            $tasks = $this->repository->matchingWithGroup($criteria, $group);
        } else {
            $company = $this->getUser()->getCompany();
            $criteria->andWhere(new Comparison('company', Comparison::EQ, $company));
            $tasks = $this->repository->matching($criteria);
        }

        $context = [AbstractNormalizer::GROUPS => 'public'];
        if ($fields = $form->get('fields')->getData()) {
            $context[AbstractNormalizer::ATTRIBUTES] = $fields;
        }
        $tasks = $this->serializer->normalize($tasks, null, $context);

        return $this->json($tasks);
    }

    /**
     * @Route("/tasks/{id}", name="app_task_show")
     */
    public function show(Task $task): Response
    {
        $this->denyAccessUnlessGranted('view', $task);

        $context = [AbstractNormalizer::GROUPS => 'public'];
        $task = $this->serializer->normalize($task, null, $context);

        return $this->json($task);
    }
}
