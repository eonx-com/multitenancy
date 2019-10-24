<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\LoyaltyCorp\Search\Handlers;

use LoyaltyCorp\Search\Interfaces\TransformableSearchHandlerInterface;

/**
 * @coversNothing
 */
final class SearchHandlerStub implements TransformableSearchHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getFillIterable(): iterable
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getMappings(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getSettings(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getHandledClasses(): array
    {
        return [SearchableStub::class];
    }

    /**
     * @inheritdoc
     */
    public function getIndexName(): string
    {
        return 'search-index';
    }

    /**
     * @inheritdoc
     */
    public function getSearchId(object $object)
    {
        return \method_exists($object, 'getSearchId') ? $object->getSearchId() : null;
    }

    /**
     * @inheritdoc
     */
    public function transform($object = null): ?array
    {
        return \method_exists($object, 'toArray') ? $object->toArray() : null;
    }
}
