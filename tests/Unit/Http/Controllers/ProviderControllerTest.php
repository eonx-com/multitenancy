<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Http\Controllers;

use EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Http\Controllers\ProviderController;
use LoyaltyCorp\Multitenancy\Http\Requests\Providers\ProviderCreateRequest;
use LoyaltyCorp\Multitenancy\Http\Requests\Providers\ProviderModifyRequest;
use LoyaltyCorp\Multitenancy\Services\Providers\Interfaces\ProviderServiceInterface;
use LoyaltyCorp\Multitenancy\Services\Providers\ProviderService;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Services\Providers\ProviderServiceStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\EoneoPay\Externals\ORM\EntityManagerSpy;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\EoneoPay\Externals\ORM\EntityManagerStub;
use Tests\LoyaltyCorp\Multitenancy\TestCases\Unit\ControllerTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Http\Controllers\ProviderController
 */
final class ProviderControllerTest extends ControllerTestCase
{
    /**
     * Tests that the `create` method on the controller is successful.
     *
     * @return void
     */
    public function testProviderCreateExistingSuccess(): void
    {
        $entity = new Provider('test-provider', 'Test Provider');
        $entityManager = new EntityManagerSpy();
        $entityManager->setRepositoryEntity($entity);
        $controller = $this->getControllerInstance($entityManager, new ProviderService($entityManager));
        $json = <<<'JSON'
{
    "id": "test-provider",
    "name": "Test Provider"
}
JSON;

        $expected = [
            'id' => 'test-provider',
            'name' => 'Test Provider',
        ];

        /**
         * @var \LoyaltyCorp\Multitenancy\Http\Requests\Providers\ProviderCreateRequest $request
         */
        $request = $this->requestTestHelper->buildUnvalidatedRequest(ProviderCreateRequest::class, $json);
        $result = $controller->create($request);

        self::assertSame($expected, $result->getContent());
        self::assertCount(0, $entityManager->getPersisted());
    }

    /**
     * Tests that the `create` method on the controller is successful.
     *
     * @return void
     */
    public function testProviderCreateSuccess(): void
    {
        $entityManager = new EntityManagerSpy();
        $controller = $this->getControllerInstance($entityManager, new ProviderService($entityManager));
        $json = <<<'JSON'
{
    "id": "test-provider",
    "name": "Test Provider"
}
JSON;

        $expected = [
            'id' => 'test-provider',
            'name' => 'Test Provider',
        ];

        /**
         * @var \LoyaltyCorp\Multitenancy\Http\Requests\Providers\ProviderCreateRequest $request
         */
        $request = $this->requestTestHelper->buildUnvalidatedRequest(ProviderCreateRequest::class, $json);
        $result = $controller->create($request);

        self::assertSame($expected, $result->getContent());
        self::assertTrue($entityManager->isFlushed());
        self::assertCount(1, $entityManager->getPersisted());
    }

    /**
     * Tests that the `modify` method on the controller is successful.
     *
     * @return void
     */
    public function testProviderModifySuccess(): void
    {
        $entityManager = $this->getEntityManager();
        $provider = new Provider('test-provider', 'Test Provider');
        $entityManager->persist($provider);
        $entityManager->flush();
        $controller = $this->getControllerInstance($entityManager);
        $json = <<<'JSON'
{
    "name": "Something Different"
}
JSON;

        $expected = [
            'id' => 'test-provider',
            'name' => 'Something Different',
        ];

        /**
         * @var \LoyaltyCorp\Multitenancy\Http\Requests\Providers\ProviderModifyRequest $request
         */
        $request = $this->requestTestHelper->buildUnvalidatedRequest(ProviderModifyRequest::class, $json);
        $result = $controller->modify($provider, $request);

        self::assertSame($expected, $result->getContent());
        self::assertSame('Something Different', $provider->getName());
    }

    /**
     * Creates a new instance of the Provider controller instance.
     *
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface|null $entityManager
     * @param \LoyaltyCorp\Multitenancy\Services\Providers\Interfaces\ProviderServiceInterface|null $providerService
     *
     * @return \LoyaltyCorp\Multitenancy\Http\Controllers\ProviderController
     */
    private function getControllerInstance(
        ?EntityManagerInterface $entityManager = null,
        ?ProviderServiceInterface $providerService = null
    ): ProviderController {
        return new ProviderController(
            $entityManager ?? new EntityManagerStub(),
            $providerService ?? new ProviderServiceStub()
        );
    }
}
