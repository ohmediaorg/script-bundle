<?php

namespace OHMedia\ScriptBundle\Form;

use OHMedia\ScriptBundle\Entity\Script;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ScriptType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $script = $options['data'];

        $builder->add('name', TextType::class, [
            'help' => 'For internal reference only.',
        ]);

        $builder->add('content', TextareaType::class, [
            'help' => 'This content will be output as is.',
            'attr' => [
                'rows' => 10,
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Script::class,
        ]);
    }
}
