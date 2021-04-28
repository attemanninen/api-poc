<?php

namespace App\Form;

use App\Form\DataTransferObject\GroupRoleData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GroupRoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user', UsernameTextType::class, [
                'required' => true,
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'View task' => 'ROLE_GROUP_TASK_VIEW',
                    'Add task' => 'ROLE_GROUP_TASK_ADD',
                    'Edit task' => 'ROLE_GROUP_TASK_EDIT',
                    'Delete task' => 'ROLE_GROUP_TASK_DELETE',
                ],
                'expanded' => true,
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GroupRoleData::class,
        ]);
    }
}
