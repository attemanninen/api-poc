<?php

namespace App\Controller\UI;

use App\Entity\Group;
use App\Entity\Task;
use App\Exception\FormValidationException;
use App\Form\UI\TaskFilterType;
use App\Form\ListParametersType;
use App\Repository\TaskRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

/**
 * @Route("/ui", name="app_ui_task_")
 */
class TaskController extends AbstractController
{
    /**
     * @var TaskRepository
     */
    private $repository;

    public function __construct(
        TaskRepository $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * @Route("/tasks", name="list")
     */
    public function list(Request $request, Group $group = null): Response
    {
        $criteria = Criteria::create();
        $form = $this->createForm(TaskFilterType::class, $criteria);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            throw new FormValidationException($form);
        }

        $company = $this->getUser()->getCompany();
        $criteria->andWhere(new Comparison('company', Comparison::EQ, $company));
        $tasks = $this->repository->matching($criteria);

        return $this->render('task/list.html.twig', [
            'tasks' => $tasks,
            'filterForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/tasks/{id}", name="show")
     */
    public function show(Task $task): Response
    {
        $this->denyAccessUnlessGranted('view', $task);

        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }
}
