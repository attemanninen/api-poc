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
        $user = $options['user'];
        if ($options['company_teams_as_choices']) {
            $teams = $this->teamRepository->findBy(['company' => $user->getCompany()]);
        } else {
            $teams = $this->teamRepository->findByUserTeamPermissions($user);
        }

        $builder
            ->add('name_enabled', CheckboxType::class, [
                'label' => 'Name',
                'required' => false,
            ])
            ->add('name', TextType::class, [
                'label' => false,
                'required' => false,
            ])
            ->add('teams_enabled', CheckboxType::class, [
                'label' => $options['company_teams_as_choices'] ? 'Company teams' : 'Your teams',
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
        $resolver->setDefaults([
            'method' => 'GET',
            'company_teams_as_choices' => true
        ]);
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

        if ($forms['name_enabled']->getData()) {
            $name = $forms['name']->getData();
            $viewData->andWhere(Criteria::expr()->eq('name', $name));
        }
    }
}
