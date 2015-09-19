<?php


namespace UrlShortenerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class createUrlShortcutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("url", "url", ["label" => "Enter Url ", 'attr' => ["placeholder" => "Type URL with http://"]])
            ->add("shorten", "submit", ["label" => "Shorten"]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => false,
        ));
    }

    public function getName()
    {
        return 'create_url_shortcut';
    }
}