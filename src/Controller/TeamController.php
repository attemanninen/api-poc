<?php

namespace App\Controller;

use App\Entity\Team;
use App\Exception\FormValidationException;
use App\Form\ListParametersType;
use App\Repository\TeamRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class TeamController extends AbstractController
{
    /**
     * @Route("/teams", name="app_team_list")
     */
    public function list(
        Request $request,
        TeamRepository $repository,
        SerializerInterface $serializer,
    ): Response {
        $criteria = Criteria::create();
        $form = $this->createForm(ListParametersType::class, $criteria, [
            'model' => Team::class,
        ]);
        $form->submit($request->query->all());

        if (!$form->isValid()) {
            throw new FormValidationException($form);
        }

        $company = $this->getUser()->getCompany();
        $criteria->andWhere(new Comparison('company', Comparison::EQ, $company));
        $teams = $repository->matching($criteria);

        $context = [AbstractNormalizer::GROUPS => 'public'];

        if ($fields = $form->get('fields')->getData()) {
            $context[AbstractNormalizer::ATTRIBUTES] = $fields;
        }
        $teams = $serializer->normalize($teams, null, $context);

        return $this->json($teams);
    }
}
