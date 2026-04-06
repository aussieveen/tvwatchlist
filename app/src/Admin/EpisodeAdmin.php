<?php

declare(strict_types=1);

namespace App\Admin;

use App\Document\Episode;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * @extends AbstractAdmin<Episode>
 */
class EpisodeAdmin extends AbstractAdmin
{
    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->addIdentifier('id')
            ->add('seriesTitle')
            ->add('season')
            ->add('episode')
            ->add('title')
            ->add('platform')
            ->add('status', 'choice', [
                'choices' => array_flip(Episode::VALID_STATUSES),
            ])
            ->add('airDate')
            ->add('watched');
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('seriesTitle')
            ->add('platform', null, [
                'field_type' => ChoiceType::class,
                'field_options' => [
                    'choices' => array_combine(Episode::AVAILABLE_PLATFORMS, Episode::AVAILABLE_PLATFORMS),
                ],
            ])
            ->add('status', null, [
                'field_type' => ChoiceType::class,
                'field_options' => [
                    'choices' => array_flip(Episode::VALID_STATUSES),
                ],
            ])
            ->add('watched');
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('id')
            ->add('seriesTitle')
            ->add('season')
            ->add('episode')
            ->add('title')
            ->add('description')
            ->add('tvdbEpisodeId')
            ->add('tvdbSeriesId')
            ->add('poster')
            ->add('universe')
            ->add('platform')
            ->add('status')
            ->add('airDate')
            ->add('watched');
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('watched', CheckboxType::class, ['required' => false])
            ->add('platform', ChoiceType::class, [
                'choices' => array_combine(Episode::AVAILABLE_PLATFORMS, Episode::AVAILABLE_PLATFORMS),
                'required' => false,
            ])
            ->add('universe', null, ['required' => false])
            ->add('status', ChoiceType::class, [
                'choices' => array_flip(Episode::VALID_STATUSES),
                'required' => false,
            ]);
    }
}
