<?php

namespace App\Form;

use App\Entity\Team;
use App\Form\DataTransferObject\TaskData;
use App\Repository\TeamRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    private $teamRepository;

    public function __construct(TeamRepository $teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $teams = $this->teamRepository->findBy(['company' => $options['company']]);
        $builder
            ->add('name', TextType::class, [
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'required' => true,
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
