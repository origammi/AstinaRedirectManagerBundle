<?php

namespace Astina\Bundle\RedirectManagerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class GroupFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array('label' => 'form.name', 'translation_domain' => 'AstinaRedirectManagerBundle'))
            ->add('priority', 'number',  array('label' => 'form.priority', 'translation_domain' => 'AstinaRedirectManagerBundle'))
        ;
    }

    public function getName()
    {
        return 'armb_group_type';
    }
}
