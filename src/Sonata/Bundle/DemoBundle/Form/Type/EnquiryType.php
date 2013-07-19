<?php

namespace Sonata\Bundle\DemoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EnquiryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 
            'text', array(
                'label' => 'Full Name',
                'attr'  => array(
                    'placeholder' => 'Enter your full name.'
                )
            )
        );

        $builder->add('email', 
            'email', array(
                'label' => 'E-Mail',
                'attr'  => array(
                    'placeholder' => 'Enter your email address.'
                )
            )
        );

        $builder->add('subject', 
            'text', array(
                'label' => 'Subject',
                'attr'  => array(
                    'placeholder' => 'Subject.'
                )
            )
        );

        $builder->add('comment', 
            'textarea', array(
                'label' => 'Comment',
                'attr'  => array(
                    'placeholder' => 'Please type something here...',
                    'rows'        => 3
                )
            )
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                'data_class' => 'Sonata\Bundle\DemoBundle\Entity\Enquiry',
            )
        );
    }

    public function getName()
    {
        return 'enquiry';
    }
}