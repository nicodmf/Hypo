<?php

namespace Hypo\TestBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('width')
            ->add('height')
            ->add('path')
            ->add('mimetype')
            ->add('originalName')
            ->add('size')
            ->add('legend')
            ->add('description')
        ;
    }

    public function getName()
    {
        return 'hypo_testbundle_imagetype';
    }
}
