<?php

declare(strict_types=1);

namespace App\Admin;

use App\Document\Episode;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class EpisodeAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('seriesTitle', TextType::class)
            ->add('season', null, [
                'attr' => ['min' => 1],
            ])
            ->add('episode', null, [
                'attr' => ['min' => 1],
            ])
            ->add('tvdbEpisodeId', TextType::class)
            ->add('tvdbSeriesId', TextType::class)
            ->add('universe', TextType::class)
            ->add('platform', ChoiceType::class, [
                'choices' => array_combine(
                    Episode::AVAILABLE_PLATFORMS,
                    Episode::AVAILABLE_PLATFORMS
                ),
            ])
            ->add('status', ChoiceType::class, [
                'choices' => array_flip(Episode::VALID_STATUSES),
            ])
            ->add('airDate', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('watched', null, [
                'required' => false,
            ]);
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id')
            ->add('title')
            ->add('seriesTitle')
            ->add('season')
            ->add('episode')
            ->add('platform')
            ->add('status')
            ->add('airDate')
            ->add('watched', null, [
                'editable' => true,
            ])
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('title')
            ->add('seriesTitle')
            ->add('season')
            ->add('episode')
            ->add('platform')
            ->add('status')
            ->add('watched');
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('id')
            ->add('title')
            ->add('description')
            ->add('seriesTitle')
            ->add('season')
            ->add('episode')
            ->add('tvdbEpisodeId')
            ->add('tvdbSeriesId')
            ->add('universe')
            ->add('platform')
            ->add('status')
            ->add('airDate')
            ->add('watched');
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->remove('create');
        $collection->remove('edit');
    }
}
