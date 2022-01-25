<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Comment;
use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CommentAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content')
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'data' => new \DateTime()
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'lastname'
            ])
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'name'
            ])
            ->add('enregistrer', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
