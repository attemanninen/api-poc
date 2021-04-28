<?php

namespace App\Form;

use App\Form\DataTransformer\UsernameTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsernameTextType extends AbstractType
{
    /**
     * @var UsernameTransformer
     */
    private $transformer;

    public function __construct(UsernameTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'invalid_message' => 'Given user does not exist',
        ]);
    }

    public function getParent(): string
    {
        return TextType::class;
    }
}
