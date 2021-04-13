<?php

namespace App\Form;

use App\Form\DataMapper\CriteriaDataMapper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListParametersType extends AbstractType
{
    /**
     * {@inheritDoc}
     *
     * @todo 'search' keyword should be 3 chars minimum?
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pageSize', NumberType::class, [
                'empty_data' => '100',
            ])
            ->add('page', NumberType::class, [
                'empty_data' => '0',
            ])
            ->add('orderBy', TextType::class)
            ->add('filters', TextType::class)
            ->add('search', TextType::class)
            ->add('fields', TextType::class, [
                'mapped' => false,
            ])
            ->setDataMapper(new CriteriaDataMapper($options['model']))
        ;

        $builder->get('fields')
            ->addModelTransformer(new CallbackTransformer(
                function ($fieldsAsArray) {
                    return '';
                },
                function ($fieldsAsString) {
                    if (!$fieldsAsString) {
                        return [];
                    }
                    return explode(',', $fieldsAsString);
                }
            ))
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('empty_data', null);
        $resolver->setRequired('model');
    }
}
