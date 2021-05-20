<?php

namespace App\Controller\UI;

use App\Entity\Team;
use App\Entity\TeamPermission;
use App\Exception\FormValidationException;
use App\Form\DataTransferObject\TeamData;
use App\Form\DataTransferObject\TeamPermissionData;
use App\Form\TeamType;
use App\Form\UI\TeamFilterType;
use App\Repository\TeamRepository;
use App\Repository\TeamPermissionRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ui/teams", name="app_ui_team_")
 */
class TeamController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TeamRepository
     */
    private $repository;

    public function __construct(
        EntityManagerInterface $entityManager,
        TeamRepository $repository
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request): Response
    {
        $teamData = new TeamData();
        $teamData->teamPermissions = [new TeamPermissionData()];
        $form = $this->createForm(TeamType::class, $teamData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $team = new Team(
                $this->getUser()->getCompany(),
                $teamData->name
            );
            $this->entityManager->persist($team);

            foreach ($teamData->teamPermissions as $teamPermissionData) {
                $teamPermission = new TeamPermission(
                    $team,
                    $teamPermissionData->user,
                    $teamPermissionData->permissions
                );
                $this->entityManager->persist($teamPermission);
            }

            $this->entityManager->flush();

            $this->addFlash('success', 'Team created');

            return $this->redirectToRoute('app_ui_team_show', [
                'id' => $team->getId(),
            ]);
        }

        return $this->render('team/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/", name="list")
     */
    public function list(Request $request): Response
    {
        $criteria = Criteria::create();
        $form = $this->createForm(TeamFilterType::class, $criteria);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            throw new FormValidationException($form);
        }

        $company = $this->getUser()->getCompany();
        $criteria->andWhere(new Comparison('company', Comparison::EQ, $company));
        $teams = $this->repository->matching($criteria);

        return $this->render('team/list.html.twig', [
            'teams' => $teams,
            'filterForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/shared", name="list_shared")
     */
    public function listShared(Request $request): Response
    {
        $criteria = Criteria::create();
        $form = $this->createForm(TeamFilterType::class, $criteria);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            throw new FormValidationException($form);
        }

        $teams = $this
            ->repository
            ->matchingWithUserTeamPermissions($criteria, $this->getUser());

        return $this->render('team/list.html.twig', [
            'teams' => $teams,
            'filterForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="show")
     */
    public function show(
        Team $team,
        TeamPermissionRepository $teamPermissionRepository
    ): Response {
        $this->denyAccessUnlessGranted('view', $team);

        $teamPermissions = $teamPermissionRepository->findBy(['team' => $team]);

        return $this->render('team/show.html.twig', [
            'team' => $team,
            'teamPermissions' => $teamPermissions,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit")
     */
    public function edit(
        Team $team,
        TeamPermissionRepository $teamPermissionRepository,
        Request $request
    ): Response {
        $this->denyAccessUnlessGranted('edit', $team);

        $teamPermissions = $teamPermissionRepository->findBy(['team' => $team]);
        $teamData = TeamData::fromTeam($team, $teamPermissions);

        $form = $this->createForm(TeamType::class, $teamData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $teamData->updateTeam($team);

            foreach ($teamData->teamPermissions as $teamPermissionData) {
                $newTeamPermission = true;
                foreach ($teamPermissions as $teamPermission) {
                    if ($teamPermissionData->user === $teamPermission->getUser()) {
                        $teamPermissionData->updateTeamPermission($teamPermission);
                        $newTeamPermission = false;
                    }
                }
                if ($newTeamPermission) {
                    $teamPermission = new TeamPermission(
                        $team,
                        $teamPermissionData->user,
                        $teamPermissionData->permissions
                    );
                    $this->entityManager->persist($teamPermission);
                }
            }

            // Check team permissions to be removed
            foreach ($teamPermissions as $teamPermission) {
                $found = false;
                foreach ($teamData->teamPermissions as $teamPermissionData) {
                    if ($teamPermission->getUser() === $teamPermissionData->user) {
                        $teamPermissionData->updateTeamPermission($teamPermission);
                        $found = true;
                    }
                }
                if (!$found) {
                    $this->entityManager->remove($teamPermission);
                }
            }

            $this->entityManager->flush();

            $this->addFlash('success', 'Team updated');

            return $this->redirectToRoute('app_ui_team_show', [
                'id' => $team->getId(),
            ]);
        }

        return $this->render('team/edit.html.twig', [
            'team' => $team,
            'form' => $form->createView(),
        ]);
    }
}
