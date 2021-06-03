<?php

namespace App\Form;

use App\Entity\Customer;
use App\Entity\Team;
use App\Form\DataTransferObject\TaskData;
use App\Repository\CustomerRepository;
use App\Repository\TeamRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    private CustomerRepository $customerRepository;
    private TeamRepository $teamRepository;

    public function __construct(
        CustomerRepository $customerRepository,
        TeamRepository $teamRepository
    ) {
        $this->customerRepository = $customerRepository;
        $this->teamRepository = $teamRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $customers = $this->customerRepository->findBy(
            ['company' => $options['company']],
            ['name' => 'ASC']
        );
        $teams = $this->teamRepository->findBy(
            ['company' => $options['company']],
            ['name' => 'ASC']
        );

        $builder
            ->add('name', TextType::class, [
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('customer', EntityType::class, [
                'class' => Customer::class,
                'choices' => $customers,
                'required' => false,
                'choice_label' => 'name',
            ])
            ->add('teams', EntityType::class, [
                'class' => Team::class,
                'choice_label' => 'name',
                'choices' => $teams,
                'required' => false,
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TaskData::class,
            'label' => false,
        ]);
        $resolver->setRequired('company');
    }
}
