<?php

declare(strict_types=1);

namespace App\Admin;

use App\Document\History;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;

final class HistoryAdmin extends AbstractAdmin
{
    protected function configureFormFields(FormMapper $form): void
    {
        $form->add('seriesTitle')
            ->add('episodeTitle')
            ->add('universe', null, [
                'required' => false,
            ])
            ->add('airDate', 'sonata_type_datetime_picker')
            ->add('watchedAt', 'sonata_type_datetime_picker');
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter->add('seriesTitle')
            ->add('episodeTitle')
            ->add('universe')
            ->add('airDate')
            ->add('watchedAt');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list->addIdentifier('id')
            ->add('seriesTitle')
            ->add('episodeTitle')
            ->add('universe')
            ->add('airDate')
            ->add('watchedAt')
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show->add('id')
            ->add('seriesTitle')
            ->add('episodeTitle')
            ->add('universe')
            ->add('airDate')
            ->add('watchedAt');
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->remove('create');
        $collection->remove('edit');
    }
}
