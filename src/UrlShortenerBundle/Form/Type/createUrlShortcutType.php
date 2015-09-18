<?php


namespace UrlShortenerBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class createUrlShortcutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("url", "url", ["label" => "Enter Url "])
            ->add("shorten", "submit", ["label" => "Shorten"]);
    }

    public function getDefaultOptions(array $options)
    {
        return array('data_class' => 'UrlShortener\Entity\UrlShortcut');
    }

    public function getName()
    {
        return 'create_url_shortcut';
    }
}