<?php

namespace App\Form;

use App\Entity\Livraison;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LivraisonFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('idCommande')
            ->add('nomLivreur')
            ->add('dateLivraison')
            ->add('etat', ChoiceType::class, [
                'choices'  => [
                    'Traitée' => "Traitée",
                    'Expédiée' => "Expédiée",
                    'En Route' => "En Route",
                    'Arrivée' => "Arrivée",
                ],])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Livraison::class,
        ]);
    }
}
