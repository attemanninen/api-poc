<?php

namespace App\Form\UI;

use App\Entity\Team;
use App\Repository\TeamRepository;
use Doctrine\Common\Collections\Criteria;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\DataMapperInterface;

class TaskFilterType extends AbstractType implements DataMapperInterface
{
    private $teamRepository;

    public function __construct(TeamRepository $teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $teams = $this->teamRepository->findByUserTeamPermissions($options['user']);
        $builder
            ->add('enable_name', CheckboxType::class, [
                'label' => 'Name',
                'required' => false,
            ])
            ->add('name', TextType::class, [
                'label' => false,
                'required' => false,
            ])
            ->add('enable_teams', CheckboxType::class, [
                'label' => 'Teams',
                'required' => false,
            ])
            ->add('teams', EntityType::class, [
                'class' => Team::class,
                'choice_label' => 'name',
                'choices' => $teams,
                'required' => false,
                'multiple' => true,
                'expanded' => true,
            ])
            ->setDataMapper($this)
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('method', 'GET');
        $resolver->setRequired('user');
    }

    /**
     * {@inheritDoc}
     */
    public function mapDataToForms($viewData, iterable $forms)
    {
        // Not needed for now.
    }

    /**
     * {@inheritDoc}
     */
    public function mapFormsToData(iterable $forms, &$viewData)
    {
        $forms = iterator_to_array($forms);

        if ($forms['enable_name']->getData()) {
            $name = $forms['name']->getData();
            $viewData->andWhere(Criteria::expr()->eq('name', $name));
        }
    }
}
