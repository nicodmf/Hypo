<?php

namespace Hypo\TestBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Hypo\DocumentBundle\Form\FileType as OFileType;

class FileType extends OFileType
{
    /*public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('file')
            //->add('path')
            //->add('mimetype')
            //->add('originalName')
            //->add('size')
            ->add('legend')
            ->add('description')
        ;
    }*/

    public function getName()
    {
        return 'hypo_testbundle_filetype';
    }
}
