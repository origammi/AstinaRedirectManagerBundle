<?php

namespace Astina\Bundle\RedirectManagerBundle\Form\Type;

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

    /**
     * Returns default options for form type.
     *
     * @param array $options
     *
     * @return array
     */
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
        return 'armb_map_type';
    }
}
