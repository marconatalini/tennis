<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 11/06/2019
 * Time: 22:52
 */

namespace App\Form;

use App\Entity\Contatto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\EmailValidator;

class ContattoType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contatto::class
        ]);
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('messaggio', TextareaType::class, [
            'attr' => ['rows' => 8],
            'required' => true
            ])
            ->add('email', EmailType::class, [
                'required' => true,
            ])
            ->add('invia', SubmitType::class);
    }


}