<?php

namespace Astina\Bundle\RedirectManagerBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('urlFrom', TextType::class, array(
                'label' => 'form.urlFrom',
                'translation_domain' => 'AstinaRedirectManagerBundle',
            ))
            ->add('urlFromIsRegexPattern', CheckboxType::class, array(
                'label' => 'form.urlFromIsRegexPattern',
                'translation_domain' => 'AstinaRedirectManagerBundle',
                'required' => false,
                'attr' => $hideAdvancedSettings ? array('data-advanced-field' => '') : array(),
            ))
            ->add('urlFromIsNoCase', CheckboxType::class, array(
                'label' => 'form.urlFromIsNoCase',
                'translation_domain' => 'AstinaRedirectManagerBundle',
                'required' => false,
                'attr' => $hideAdvancedSettings ? array('data-advanced-field' => '') : array(),
            ))
            ->add('urlTo', TextType::class, array(
                'label' => 'form.urlTo',
                'translation_domain' => 'AstinaRedirectManagerBundle',
            ))
            ->add('group', EntityType::class, array(
                'label' => 'form.group',
                'translation_domain' => 'AstinaRedirectManagerBundle',
                'required' => false,
                'choice_label' => 'name',
                'class' => 'AstinaRedirectManagerBundle:Group',
            ))
            ->add('host', TextType::class, array(
                'label' => 'form.host',
                'translation_domain' => 'AstinaRedirectManagerBundle',
                'required' => false,
                'attr' => $hideAdvancedSettings ? array('data-advanced-field' => '') : array(),
            ))
            ->add('hostIsRegexPattern', CheckboxType::class, array(
                'label' => 'form.hostIsRegexPattern',
                'translation_domain' => 'AstinaRedirectManagerBundle',
                'required' => false,
                'attr' => $hideAdvancedSettings ? array('data-advanced-field' => '') : array(),
            ))
            ->add('hostRegexPatternNegate', CheckboxType::class, array(
                'label' => 'form.hostRegexPatternNegate',
                'translation_domain' => 'AstinaRedirectManagerBundle',
                'required' => false,
                'attr' => $hideAdvancedSettings ? array('data-advanced-field' => '') : array(),
            ))
            ->add('redirectHttpCode', ChoiceType::class, array(
                'label' => 'form.redirectHttpCode',
                'translation_domain' => 'AstinaRedirectManagerBundle',
		'choices_as_values' => true,
                'choices' => array(
                    '301 Moved Permanently' => 301,
                    '302 Found' => 302,
                    '303 See Other' => 303,
                ),
                'attr' => $hideAdvancedSettings ? array('data-advanced-field' => '') : array(),
            ))
            ->add('countRedirects', CheckboxType::class, array(
                'label' => 'form.countRedirects',
                'translation_domain' => 'AstinaRedirectManagerBundle',
                'required' => false,
            ))
            ->add('comment', TextareaType::class, array(
                'label' => 'form.comment',
                'translation_domain' => 'AstinaRedirectManagerBundle',
                'required' => false,
                'attr' => $hideAdvancedSettings ? array('data-advanced-field' => '') : array(),
            ))
        ;
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
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
}
