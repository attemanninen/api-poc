<?php

namespace App\Controller\UI;

use App\Entity\Task;
use App\Entity\TaskTeam;
use App\Exception\FormValidationException;
use App\Form\DataTransferObject\TaskData;
use App\Form\TaskType;
use App\Form\UI\TaskFilterType;
use App\Repository\TaskRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ui/tasks", name="app_ui_task_")
 */
class TaskController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TaskRepository
     */
    private $repository;

    public function __construct(
        EntityManagerInterface $entityManager,
        TaskRepository $repository,
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request): Response
    {
        $taskData = new TaskData();
        $company = $this->getUser()->getCompany();
        $form = $this->createForm(TaskType::class, $taskData, [
            'company' => $company,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task = new Task($company, $taskData->name);
            $taskData->updateTask($task);

            foreach ($taskData->teams as $team) {
                $taskTeam = new TaskTeam($task, $team);
                $this->entityManager->persist($taskTeam);
            }
            $this->entityManager->persist($task);
            $this->entityManager->flush();

            $this->addFlash('success', 'Task created');

            return $this->redirectToRoute('app_ui_task_show', [
                'id' => $task->getId(),
            ]);
        }

        return $this->render('task/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/", name="list")
     */
    public function list(Request $request): Response
    {
        $criteria = Criteria::create();
        $form = $this->createForm(TaskFilterType::class, $criteria, [
            'user' => $this->getUser(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            throw new FormValidationException($form);
        }

        $teams = $form->get('teams')->getData();

        if ($form->get('teams_enabled')->getData() && $teams) {
            $tasks = $this->repository->matchingWithTeams($criteria, $teams);
        } else {
            $company = $this->getUser()->getCompany();
            $criteria->andWhere(new Comparison('company', Comparison::EQ, $company));
            $tasks = $this->repository->matching($criteria);
        }

        return $this->render('task/list.html.twig', [
            'tasks' => $tasks,
            'filterForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/shared", name="list_shared")
     */
    public function listShared(Request $request): Response
    {
        $criteria = Criteria::create();
        $form = $this->createForm(TaskFilterType::class, $criteria, [
            'user' => $this->getUser(),
            'company_teams_as_choices' => false,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            throw new FormValidationException($form);
        }

        $teams = $form->get('teams')->getData();

        if ($form->get('teams_enabled')->getData() && $teams) {
            $tasks = $this->repository->matchingWithTeams($criteria, $teams);
        } else {
            $tasks = $this->repository->matchingWithAnyTeam($criteria, $this->getUser());
        }

        return $this->render('task/list.html.twig', [
            'tasks' => $tasks,
            'filterForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="show")
     */
    public function show(Task $task): Response
    {
        $this->denyAccessUnlessGranted('view', $task);

        return $this->render('task/show.html.twig', [
            'task' => $task,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit")
     */
    public function edit(
        Task $task,
        Request $request,
    ): Response {
        $this->denyAccessUnlessGranted('edit', $task);

        $taskData = TaskData::fromTask($task);
        $form = $this->createForm(TaskType::class, $taskData, [
            'company' => $this->getUser()->getCompany(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $taskData->updateTask($task);

            foreach ($taskData->teams as $team) {
                if (!$task->getTeam($team)) {
                    $taskTeam = new TaskTeam($task, $team);
                    $this->entityManager->persist($taskTeam);
                }
            }

            foreach ($task->getTeams() as $taskTeam) {
                $found = false;

                foreach ($taskData->teams as $team) {
                    if ($taskTeam->getTeam() === $team) {
                        $found = true;
                    }
                }

                if (!$found) {
                    $this->entityManager->remove($taskTeam);
                }
            }

            $this->entityManager->flush();

            $this->addFlash('success', 'Task updated');

            return $this->redirectToRoute('app_ui_task_show', [
                'id' => $task->getId(),
            ]);
        }

        return $this->render('task/edit.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }
}
