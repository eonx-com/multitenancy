<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Services\Search;

use EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface;
use LoyaltyCorp\Multitenancy\Services\Search\Transformers\ProviderIndexTransformer;
use stdClass;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Database\Entities\EntityHasProviderStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\EoneoPay\Externals\ORM\EntityManagerStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\LoyaltyCorp\Search\Handlers\ProviderAwareSearchHandlerStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\LoyaltyCorp\Search\Handlers\SearchHandlerStub;
use Tests\LoyaltyCorp\Multitenancy\TestCases\AppTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Services\Search\Transformers\ProviderIndexTransformer
 */
final class ProviderIndexTransformerTest extends AppTestCase
{
    /**
     * Test that transform index name returns expected index name.
     *
     * @return void
     */
    public function testTransformIndexName(): void
    {
        $object = new stdClass();
        $handler = new SearchHandlerStub();
        $entityManager = new EntityManagerStub();
        $transformer = $this->getTransformer($entityManager);

        $expectedIndexName = 'search-index';

        $actualIndexName = $transformer->transformIndexName($handler, $object);

        self::assertSame($expectedIndexName, $actualIndexName);
    }

    /**
     * Test that transform index name returns expected provider specific index name.
     *
     * @return void
     */
    public function testTransformIndexNameProviderAware(): void
    {
        $object = new EntityHasProviderStub('acmeIncId', 'Acme Inc');
        $handler = new ProviderAwareSearchHandlerStub();
        $entityManager = new EntityManagerStub([$object]);
        $transformer = $this->getTransformer($entityManager);

        $expectedIndexName = 'provider-aware-index_acmeIncId';

        $actualIndexName = $transformer->transformIndexName($handler, $object);

        self::assertSame($expectedIndexName, $actualIndexName);
    }

    /**
     * Test that transform index names with certain provider will return expected
     * array of index names.
     *
     * @return void
     */
    public function testTransformIndexNames(): void
    {
        $object = new EntityHasProviderStub('xyzCorpId', 'Xyz Corp');
        $handler = new ProviderAwareSearchHandlerStub();
        $entityManager = new EntityManagerStub([$object]);
        $transformer = $this->getTransformer($entityManager);

        $expectedIndexNames = [
            'provider-aware-index_xyzCorpId',
        ];

        $actualIndexNames = $transformer->transformIndexNames($handler);

        self::assertSame($expectedIndexNames, $actualIndexNames);
    }

    /**
     * Get provider index transformer.
     *
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface $entityManager
     *
     * @return \LoyaltyCorp\Multitenancy\Services\Search\Transformers\ProviderIndexTransformer
     */
    private function getTransformer(EntityManagerInterface $entityManager): ProviderIndexTransformer
    {
        return new ProviderIndexTransformer($entityManager);
    }
}
