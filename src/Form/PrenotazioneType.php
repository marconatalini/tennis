<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 11/05/2019
 * Time: 13:45
 */

namespace App\Form;

use App\Entity\Prenotazione;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrenotazioneType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Prenotazione::class
        ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start', DateTimeType::class, [
                'date_widget' => 'single_text',
                'hours' => range(7,23),
                'minutes' => [0,30],
                'attr' => [
                    'readonly' => 'readonly'
                ]
            ])
            ->add('ore', ChoiceType::class, [
                'mapped' => false,
                'choices' => [
                    '1 ora' => 1,
                    '2 ore' => 2,
                ],
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'label' => 'Utente',
                'choice_label' => 'username',
                'attr' => [
                    'disabled' => 'disabled'
                ]
            ])
            ->add('title', TextType::class, [
                'required' => false,
                'label'=> 'vs.',
                'help' => 'Nome avversario.'
            ])
            ->add('email', EmailType::class, [
                'required' => false,
                'mapped' => false,
                'label' => 'eMail',
                'help' => 'Scrivendo qui un indirizzo mail, manderemo un avviso/invito al destinatario'
            ])
            ->add('Prenota', SubmitType::class);
    }


}