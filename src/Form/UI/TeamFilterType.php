<?php

namespace App\Form\UI;

use Doctrine\Common\Collections\Criteria;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\DataMapperInterface;

class TeamFilterType extends AbstractType implements DataMapperInterface
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name_enabled', CheckboxType::class, [
                'label' => 'Name',
                'required' => false,
            ])
            ->add('name', TextType::class, [
                'label' => false,
                'required' => false,
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
