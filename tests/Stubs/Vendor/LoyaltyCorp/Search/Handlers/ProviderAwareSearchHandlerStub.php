<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\LoyaltyCorp\Search\Handlers;

use LoyaltyCorp\Search\Interfaces\ProviderAwareInterface;
use LoyaltyCorp\Search\Interfaces\TransformableSearchHandlerInterface;

/**
 * @coversNothing
 */
final class ProviderAwareSearchHandlerStub implements TransformableSearchHandlerInterface, ProviderAwareInterface
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
        return 'provider-aware-index';
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

    /**
     * {@inheritdoc}
     */
    public function getProviderId(object $object): string
    {
        return \method_exists($object, 'getExternalId') ? $object->getExternalId() : 'providerId';
    }
}
