<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Exception\FormValidationException;
use App\Form\ListParametersType;
use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class CustomerController extends AbstractController
{
    /**
     * @Route("/customers", name="app_customer_list")
     */
    public function list(
        Request $request,
        CustomerRepository $repository,
        SerializerInterface $serializer
    ): Response {
        $criteria = Criteria::create();
        $criteria->setMaxResults(100);

        $form = $this->createForm(ListParametersType::class, $criteria, [
            'model' => Customer::class,
        ]);
        $form->submit($request->query->all());

        if (!$form->isValid()) {
            throw new FormValidationException($form);
        }

        $customers = $repository->matching($criteria);

        $context = [AbstractNormalizer::GROUPS => 'public'];
        if ($fields = $form->get('fields')->getData()) {
            $context[AbstractNormalizer::ATTRIBUTES] = $fields;
        }
        $customers = $serializer->normalize($customers, null, $context);

        return $this->json($customers);
    }
}
