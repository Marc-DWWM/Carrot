<?php

namespace App\Form;

use App\Entity\User;
use Composer\Semver\Constraint\Constraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PictureProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('picture', filetype::class, [
                'label' => 'Photo de profil(jpg file)',
                'mapped' => false,
                'required' => false,
                'Constraint' => [
                    new File([
                        'maxSize' => '1024K',
                        'mimeTypes' => [
                            'application/jpg',
                            'application/x-jpg',
                        ],
                        'mimeTypeMessage' => 'Veuillez télécharger un fichier jpg valide',
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
