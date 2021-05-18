<?php

namespace App\Controller;

use App\Entity\TeamPermission;
use App\Exception\FormValidationException;
use App\Form\ListParametersType;
use App\Repository\TeamPermissionRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class TeamPermissionController extends AbstractController
{
    /**
     * @Route("/team-roles", name="app_team_role_list")
     */
    public function list(
        Request $request,
        TeamPermissionRepository $repository,
        SerializerInterface $serializer
    ): Response {
        $criteria = Criteria::create();
        $form = $this->createForm(ListParametersType::class, $criteria, [
            'model' => TeamPermission::class,
        ]);
        $form->submit($request->query->all());

        if (!$form->isValid()) {
            throw new FormValidationException($form);
        }

        $criteria->andWhere(new Comparison('user', Comparison::EQ, $this->getUser()));
        $teamPermissions = $repository->matching($criteria);

        $context = [AbstractNormalizer::GROUPS => 'public'];
        if ($fields = $form->get('fields')->getData()) {
            $context[AbstractNormalizer::ATTRIBUTES] = $fields;
        }
        $teamPermissions = $serializer->normalize($teamPermissions, null, $context);

        return $this->json($teamPermissions);
    }
}
