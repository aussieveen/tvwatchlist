<?php

declare(strict_types=1);

namespace App\Admin;

use App\Document\History;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

/**
 * @extends AbstractAdmin<History>
 */
class HistoryAdmin extends AbstractAdmin
{
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id')
            ->add('seriesTitle')
            ->add('episodeTitle')
            ->add('universe')
            ->add('airDate')
            ->add('watchedAt');
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('seriesTitle')
            ->add('universe');
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('seriesTitle')
            ->add('episodeTitle')
            ->add('universe')
            ->add('airDate')
            ->add('watchedAt');
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('seriesTitle')
            ->add('episodeTitle')
            ->add('universe', null, ['required' => false])
            ->add('airDate', DateTimeType::class, ['widget' => 'single_text'])
            ->add('watchedAt', DateTimeType::class, ['widget' => 'single_text']);
    }
}
