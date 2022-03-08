<?php

namespace App\Form;

use App\Entity\SalleSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SalleSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('nom', null, array('label' => false,'attr' => array('style' => 'width: 1000px', 'placeholder'=>'Donner le nom que vous souhaiter rechercher') ,
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SalleSearch::class,
        ]);
    }
}