<?php

namespace Astina\Bundle\RedirectManagerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class MapFormType
 *
 * @package   Astina\Bundle\RedirectManagerBundle\Form\Type
 * @author    Matej Velikonja <mvelikonja@astina.ch>
 * @copyright 2013 Astina AG (http://astina.ch)
 */
class MapFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $map = isset($options['data']) ? $options['data'] : null;
        $hideAdvancedSettings = true;
        if ($map && $map->hasAdvancedSettings()) {
            $hideAdvancedSettings = false;
        }

        $builder
            ->add('urlFrom', 'text', array(
                'label' => 'form.urlFrom',
                'translation_domain' => 'AstinaRedirectManagerBundle',
            ))
            ->add('urlFromIsRegexPattern', 'checkbox', array(
                'label' => 'form.urlFromIsRegexPattern',
                'translation_domain' => 'AstinaRedirectManagerBundle',
                'required' => false,
                'attr' => $hideAdvancedSettings ? array('data-advanced-field' => '') : array(),
            ))
            ->add('urlFromIsNoCase', 'checkbox', array(
                'label' => 'form.urlFromIsNoCase',
                'translation_domain' => 'AstinaRedirectManagerBundle',
                'required' => false,
                'attr' => $hideAdvancedSettings ? array('data-advanced-field' => '') : array(),
            ))
            ->add('urlTo', 'text', array(
                'label' => 'form.urlTo',
                'translation_domain' => 'AstinaRedirectManagerBundle',
            ))
            ->add('group', 'entity', array(
                'label' => 'form.group',
                'translation_domain' => 'AstinaRedirectManagerBundle',
                'required' => false,
                'property' => 'name',
                'class' => 'AstinaRedirectManagerBundle:Group',
            ))
            ->add('host', 'text', array(
                'label' => 'form.host',
                'translation_domain' => 'AstinaRedirectManagerBundle',
                'required' => false,
                'attr' => $hideAdvancedSettings ? array('data-advanced-field' => '') : array(),
            ))
            ->add('hostIsRegexPattern', 'checkbox', array(
                'label' => 'form.hostIsRegexPattern',
                'translation_domain' => 'AstinaRedirectManagerBundle',
                'required' => false,
                'attr' => $hideAdvancedSettings ? array('data-advanced-field' => '') : array(),
            ))
            ->add('hostRegexPatternNegate', 'checkbox', array(
                'label' => 'form.hostRegexPatternNegate',
                'translation_domain' => 'AstinaRedirectManagerBundle',
                'required' => false,
                'attr' => $hideAdvancedSettings ? array('data-advanced-field' => '') : array(),
            ))
            ->add('redirectHttpCode', 'choice', array(
                'label' => 'form.redirectHttpCode',
                'translation_domain' => 'AstinaRedirectManagerBundle',
                'choices' => array(
                    301 => '301 Moved Permanently',
                    302 => '302 Found',
                    303 => '303 See Other',
                ),
                'attr' => $hideAdvancedSettings ? array('data-advanced-field' => '') : array(),
            ))
            ->add('countRedirects', 'checkbox', array(
                'label' => 'form.countRedirects',
                'translation_domain' => 'AstinaRedirectManagerBundle',
                'required' => false,
            ))
            ->add('comment', 'textarea', array(
                'label' => 'form.comment',
                'translation_domain' => 'AstinaRedirectManagerBundle',
                'required' => false,
                'attr' => $hideAdvancedSettings ? array('data-advanced-field' => '') : array(),
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Astina\Bundle\RedirectManagerBundle\Entity\Map'
        ));
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
            'data_class' => 'Astina\Bundle\RedirectManagerBundle\Entity\Map'
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
