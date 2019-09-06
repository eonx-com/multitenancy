<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Services\Providers;

use EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface;
use LoyaltyCorp\Multitenancy\Services\Providers\ProviderService;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\EoneoPay\Externals\ORM\EntityManagerStub;
use Tests\LoyaltyCorp\Multitenancy\TestCases\AppTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Services\Providers\ProviderService
 */
final class ProviderServiceTest extends AppTestCase
{
    /**
     * Tests that the provider service successfully creates the expected Provider entity.
     *
     * @return void
     */
    public function testProviderCreation(): void
    {
        $serivce = $this->getServiceInstance();

        $provider = $serivce->create('test-provider', 'Test Provider');

        self::assertSame('test-provider', $provider->getExternalId());
        self::assertSame('Test Provider', $provider->getName());
    }

    /**
     * Creates an instance of the provider service.
     *
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface|null $entityManager
     *
     * @return \LoyaltyCorp\Multitenancy\Services\Providers\ProviderService
     */
    private function getServiceInstance(
        ?EntityManagerInterface $entityManager = null
    ): ProviderService {
        return new ProviderService($entityManager ?? new EntityManagerStub());
    }
}
