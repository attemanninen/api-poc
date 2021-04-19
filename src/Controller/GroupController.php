<?php

namespace App\Controller;

use App\Entity\Group;
use App\Exception\FormValidationException;
use App\Form\ListParametersType;
use App\Repository\GroupRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class GroupController extends AbstractController
{
    /**
     * @Route("/groups", name="app_group_list")
     */
    public function list(
        Request $request,
        GroupRepository $repository,
        SerializerInterface $serializer
    ): Response {
        $criteria = Criteria::create();
        $form = $this->createForm(ListParametersType::class, $criteria, [
            'model' => Group::class,
        ]);
        $form->submit($request->query->all());

        if (!$form->isValid()) {
            throw new FormValidationException($form);
        }

        $company = $this->getUser()->getCompany();
        $criteria->andWhere(new Comparison('company', Comparison::EQ, $company));
        $groups = $repository->matching($criteria);

        $context = [AbstractNormalizer::GROUPS => 'public'];
        if ($fields = $form->get('fields')->getData()) {
            $context[AbstractNormalizer::ATTRIBUTES] = $fields;
        }
        $groups = $serializer->normalize($groups, null, $context);

        return $this->json($groups);
    }
}
