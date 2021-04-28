<?php

namespace App\Controller\UI;

use App\Entity\Group;
use App\Entity\GroupRole;
use App\Exception\FormValidationException;
use App\Form\DataTransferObject\GroupData;
use App\Form\DataTransferObject\GroupRoleData;
use App\Form\GroupType;
use App\Form\ListParametersType;
use App\Form\UI\GroupFilterType;
use App\Repository\GroupRepository;
use App\Repository\GroupRoleRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/ui/groups", name="app_ui_group_")
 */
class GroupController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var GroupRepository
     */
    private $repository;

    public function __construct(
        EntityManagerInterface $entityManager,
        GroupRepository $repository
    ) {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request): Response
    {
        $groupData = new GroupData();
        $groupData->groupRoles = [new GroupRoleData()];
        $form = $this->createForm(GroupType::class, $groupData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $group = new Group(
                $this->getUser()->getCompany(),
                $groupData->name
            );
            $this->entityManager->persist($group);

            foreach ($groupData->groupRoles as $groupRoleData) {
                $groupRole = new GroupRole(
                    $group,
                    $groupRoleData->user,
                    $groupRoleData->roles
                );
                $this->entityManager->persist($groupRole);
            }

            $this->entityManager->flush();

            $this->addFlash('success', 'Group created');

            return $this->redirectToRoute('app_ui_group_show', [
                'id' => $group->getId(),
            ]);
        }

        return $this->render('group/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/", name="list")
     */
    public function list(Request $request): Response
    {
        $criteria = Criteria::create();
        $form = $this->createForm(GroupFilterType::class, $criteria);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            throw new FormValidationException($form);
        }

        $company = $this->getUser()->getCompany();
        $criteria->andWhere(new Comparison('company', Comparison::EQ, $company));
        $groups = $this->repository->matching($criteria);

        return $this->render('group/list.html.twig', [
            'groups' => $groups,
            'filterForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}", name="show")
     */
    public function show(
        Group $group,
        GroupRoleRepository $groupRoleRepository
    ): Response {
        $this->denyAccessUnlessGranted('view', $group);

        $groupRoles = $groupRoleRepository->findBy(['group' => $group]);

        return $this->render('group/show.html.twig', [
            'group' => $group,
            'groupRoles' => $groupRoles,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit")
     */
    public function edit(
        Group $group,
        GroupRoleRepository $groupRoleRepository,
        Request $request
    ): Response {
        $this->denyAccessUnlessGranted('edit', $group);

        $groupRoles = $groupRoleRepository->findBy(['group' => $group]);
        $groupData = GroupData::fromGroup($group, $groupRoles);

        $form = $this->createForm(GroupType::class, $groupData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $groupData->updateGroup($group);

            foreach ($groupData->groupRoles as $groupRoleData) {
                $newGrouprole = true;
                foreach ($groupRoles as $groupRole) {
                    if ($groupRoleData->user === $groupRole->getUser()) {
                        $groupRoleData->updateGroupRole($groupRole);
                        $newGrouprole = false;
                    }
                }
                if ($newGrouprole) {
                    $groupRole = new GroupRole(
                        $group,
                        $groupRoleData->user,
                        $groupRoleData->roles
                    );
                    $this->entityManager->persist($groupRole);
                }
            }

            // Check group roles to be removed
            foreach ($groupRoles as $groupRole) {
                $found = false;
                foreach ($groupData->groupRoles as $groupRoleData) {
                    if ($groupRole->getUser() === $groupRoleData->user) {
                        $groupRoleData->updateGroupRole($groupRole);
                        $found = true;
                    }
                }
                if (!$found) {
                    $this->entityManager->remove($groupRole);
                }
            }

            $this->entityManager->flush();

            $this->addFlash('success', 'Group updated');

            return $this->redirectToRoute('app_ui_group_show', [
                'id' => $group->getId(),
            ]);
        }

        return $this->render('group/edit.html.twig', [
            'group' => $group,
            'form' => $form->createView(),
        ]);
    }
}
