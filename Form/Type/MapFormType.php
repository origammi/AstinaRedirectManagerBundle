<?php

namespace Astina\RedirectManagerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MapFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('urlFrom', 'text', array('label' => 'form.urlFrom', 'translation_domain' => 'AstinaRedirectManagerBundle'))
            ->add('urlTo',   'text', array('label' => 'form.urlTo',   'translation_domain' => 'AstinaRedirectManagerBundle'))
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Astina\RedirectManagerBundle\Entity\Map'
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'astina_redirect_manager_bundle_map';
    }
}
