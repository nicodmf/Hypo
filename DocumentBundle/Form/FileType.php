<?php

namespace Hypo\DocumentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class FileType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            //->add('path')
            //->add('mimetype')
            ->add('file')
            ->add('legend')
            ->add('description')
        ;
    }
    public function getName()
    {
        return 'hypo_documentbundle_filetype';
    }
}
