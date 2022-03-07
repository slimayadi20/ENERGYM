<?php

namespace App\Form;

use App\Entity\Reclamation;
use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class ReclamationFormType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('titre')
            ->add('contenu',TextareaType::class)
            ->add('produit',EntityType::class,
            [
                'class'         => 'App\Entity\Produit',
                'query_builder'=>function(ProduitRepository $repository) {
                    $user = $this->security->getUser();
                    $prod=$repository->findProduitAchete($user) ;
                    return $prod ;
                },
                'multiple'=>false,
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reclamation::class,
        ]);
    }
}
