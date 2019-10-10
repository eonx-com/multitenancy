<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Services\Search;

use EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Services\Search\Transformers\ProviderIndexTransformer;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\EoneoPay\Externals\ORM\EntityManagerStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\LoyaltyCorp\Search\Handlers\ProviderAwareSearchHandlerStub;
use Tests\LoyaltyCorp\Multitenancy\TestCases\AppTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Services\Search\Transformers\ProviderIndexTransformer
 */
final class ProviderIndexTransformerTest extends AppTestCase
{
    /**
     * Test that transform index names with certain provider will return expected
     * array of index names.
     *
     * @return void
     */
    public function testTransformIndexNames(): void
    {
        $provider = new Provider('providerId', 'Acme Inc');
        $handler = new ProviderAwareSearchHandlerStub();
        $entityManager = new EntityManagerStub([$provider]);
        $transformer = $this->getTransformer($entityManager);

        $expectedIndexNames = [
            'provider-aware-index_providerId',
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
