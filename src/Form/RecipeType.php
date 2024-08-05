<?php

namespace App\Form;

use App\Entity\Recipe;
use DateTimeImmutable;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PostSetDataEvent;
use Symfony\Component\Form\Event\PostSubmitEvent;
use Symfony\Component\Form\Event\PreSetDataEvent;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;

use function PHPUnit\Framework\isNull;
use function Symfony\Component\Clock\now;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre', 
                'empty_data' => '', 
                /*'constraints' => [
                    new Length(min:10, max:50, minMessage: "Le titre est trop court", maxMessage: "Le titre est trop long"), 
                ]*/
                ])
            ->add('slug',TextType::class, [
                'required' => false, 
                'empty_data' => '',
                /*'constraints' => [ 
                    new Regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', message: "Ceci n'est pas un slug valide")
                ]*/
                ]) 
            ->add('duration', NumberType::class, [
                'label' => 'Durée (minutes)', 
                ])    
            ->add('content', TextareaType::class, [
                'label' => 'Contenu', 
                'empty_data' => '', 
                ])
            ->add('save', SubmitType::class, ['label' => 'Enregistrer'])
            ->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'autoSlug'])
            ->addEventListener(FormEvents::POST_SUBMIT, [$this, 'attachTimestamps'])
        ;
    }

    public function autoSlug(PreSubmitEvent $event): void
    {
        $data = $event->getData();

        if (empty($data['slug'])) {
            $slugger = new AsciiSlugger();
            $data['slug'] = strtolower($slugger->slug($data['title']));
            $event->setData($data);
        }

    }


    public function attachTimestamps(PostSubmitEvent $event): void
    {
        $data = $event->getData();

        if (!($data instanceof Recipe)) {
            return;
        }

        $data->setUpdatedAt(new DateTimeImmutable());

        if (!($data->getId())) {
            $data->setCreatedAt(new DateTimeImmutable());
        }
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}
