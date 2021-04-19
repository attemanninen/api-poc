<?php

namespace App\Controller;

use App\Entity\GroupRole;
use App\Exception\FormValidationException;
use App\Form\ListParametersType;
use App\Repository\GroupRoleRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class GroupRoleController extends AbstractController
{
    /**
     * @Route("/group-roles", name="app_group_role_list")
     */
    public function list(
        Request $request,
        GroupRoleRepository $repository,
        SerializerInterface $serializer
    ): Response {
        $criteria = Criteria::create();
        $form = $this->createForm(ListParametersType::class, $criteria, [
            'model' => GroupRole::class,
        ]);
        $form->submit($request->query->all());

        if (!$form->isValid()) {
            throw new FormValidationException($form);
        }

        $criteria->andWhere(new Comparison('user', Comparison::EQ, $this->getUser()));
        $groupRoles = $repository->matching($criteria);

        $context = [AbstractNormalizer::GROUPS => 'public'];
        if ($fields = $form->get('fields')->getData()) {
            $context[AbstractNormalizer::ATTRIBUTES] = $fields;
        }
        $groupRoles = $serializer->normalize($groupRoles, null, $context);

        return $this->json($groupRoles);
    }
}
