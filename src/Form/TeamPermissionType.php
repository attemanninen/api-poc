<?php

namespace App\Form;

use App\Entity\TeamPermission;
use App\Form\DataTransferObject\TeamPermissionData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamPermissionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', UsernameTextType::class, [
                'required' => true,
            ])
            ->add('permissions', ChoiceType::class, [
                'choices' => [
                    'Edit team' => TeamPermission::TEAM_EDIT,
                    'Remove team' => TeamPermission::TEAM_REMOVE,
                    'View task' => TeamPermission::TASK_VIEW,
                    'Create task' => TeamPermission::TASK_CREATE,
                    'Edit task' => TeamPermission::TASK_EDIT,
                    'Remove task' => TeamPermission::TASK_REMOVE,
                ],
                'expanded' => true,
                'multiple' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TeamPermissionData::class,
        ]);
    }
}
