<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Services\Requests;

use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Services\Requests\Exceptions\InvalidProviderException;
use LoyaltyCorp\Multitenancy\Services\Requests\ProviderAwareEntityFinder;
use LoyaltyCorp\Multitenancy\Services\Requests\RequestBodyContextConfigurator;
use stdClass;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Database\MultitenancyRepositoryStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\Doctrine\Common\Persistence\ManagerRegistryStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\External\ORM\EntityRepositoryStub;
use Tests\LoyaltyCorp\Multitenancy\TestCases\AppTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Services\Requests\ProviderAwareEntityFinder
 */
final class ProviderAwareEntityFinderTest extends AppTestCase
{
    /**
     * Tests that the finder returns null when no repository is returned.
     *
     * @return void
     *
     * @throws \LoyaltyCorp\Multitenancy\Services\Requests\Exceptions\InvalidProviderException
     */
    public function testGenericRepository(): void
    {
        $registry = new ManagerRegistryStub([
            'EntityClass' => new EntityRepositoryStub(),
        ]);

        $finder = new ProviderAwareEntityFinder($registry);

        $result = $finder->findOneBy('EntityClass', []);

        self::assertNull($result);
    }

    /**
     * Tests that the finder returns null when no repository is returned.
     *
     * @return void
     *
     * @throws \LoyaltyCorp\Multitenancy\Services\Requests\Exceptions\InvalidProviderException
     */
    public function testMultiRepositoryRepository(): void
    {
        $provider = new Provider('id', 'name');

        $object = new stdClass();
        $repository = new MultitenancyRepositoryStub($object);

        $registry = new ManagerRegistryStub([
            'EntityClass' => $repository,
        ]);

        $expectedCall = [
            'provider' => $provider,
            'criteria' => ['purple' => 'elephants'],
            'orderBy' => null,
        ];

        $finder = new ProviderAwareEntityFinder($registry);

        $result = $finder->findOneBy('EntityClass', ['purple' => 'elephants'], [
            RequestBodyContextConfigurator::MULTITENANCY_PROVIDER => $provider,
        ]);

        self::assertSame($object, $result);
        self::assertSame([$expectedCall], $repository->getCalls());
    }

    /**
     * Tests that the finder returns null when no repository is returned.
     *
     * @return void
     *
     * @throws \LoyaltyCorp\Multitenancy\Services\Requests\Exceptions\InvalidProviderException
     */
    public function testMultiRepositoryRepositoryNoProvider(): void
    {
        $object = new stdClass();
        $repository = new MultitenancyRepositoryStub($object);

        $registry = new ManagerRegistryStub([
            'EntityClass' => $repository,
        ]);

        $finder = new ProviderAwareEntityFinder($registry);

        $this->setExpectedException(
            InvalidProviderException::class,
            'A provider was not found in context when deserialising.'
        );

        $finder->findOneBy('EntityClass', []);
    }

    /**
     * Tests that the finder returns null when no repository is returned.
     *
     * @return void
     *
     * @throws \LoyaltyCorp\Multitenancy\Services\Requests\Exceptions\InvalidProviderException
     */
    public function testNonExistentRepository(): void
    {
        $registry = new ManagerRegistryStub(['EntityClass' => null]);

        $finder = new ProviderAwareEntityFinder($registry);

        $result = $finder->findOneBy('EntityClass', []);

        self::assertNull($result);
    }

    /**
     * Tests that the finder returns null when the repository returned is an unknown object.
     *
     * @return void
     *
     * @throws \LoyaltyCorp\Multitenancy\Services\Requests\Exceptions\InvalidProviderException
     */
    public function testUnknownRepository(): void
    {
        $registry = new ManagerRegistryStub(['EntityClass' => new stdClass()]);

        $finder = new ProviderAwareEntityFinder($registry);

        $result = $finder->findOneBy('EntityClass', []);

        self::assertNull($result);
    }
}
