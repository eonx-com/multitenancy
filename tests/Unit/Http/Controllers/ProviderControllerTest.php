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
use Tests\LoyaltyCorp\Multitenancy\Stubs\Externals\Database\EntityManagerSpy;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Externals\Database\EntityManagerStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Services\Providers\ProviderServiceStub;
use Tests\LoyaltyCorp\Multitenancy\TestCases\Unit\ControllerTestCase;

class ProviderControllerTest extends ControllerTestCase
{
    /**
     * Tests that the `create` method on the controller is successful.
     *
     * @return void
     */
    public function testProviderCreateSuccess(): void
    {
        $provider = new Provider('test-provider', 'Test Provider');
        $entityManager = new EntityManagerSpy();
        $controller = $this->getControllerInstance($entityManager, new ProviderService($entityManager));
        $request = $this->requestTestHelper->buildUnvalidatedRequest(
            ProviderCreateRequest::class,
            \json_encode(['id' => 'test-provider', 'name' => 'Test Provider'])
        );

        /**
         * @var \LoyaltyCorp\Multitenancy\Http\Requests\Providers\ProviderCreateRequest $request
         */

        $result = $controller->create($request);

        self::assertInstanceOf(Provider::class, $result->getContent());
        self::assertEquals($provider, $result->getContent());
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
        $request = $this->requestTestHelper->buildUnvalidatedRequest(
            ProviderModifyRequest::class,
            \json_encode(['name' => 'Something Different'])
        );

        /**
         * @var \LoyaltyCorp\Multitenancy\Http\Requests\Providers\ProviderModifyRequest $request
         */

        $result = $controller->modify($provider, $request);

        self::assertInstanceOf(Provider::class, $result->getContent());
        self::assertEquals($provider, $result->getContent());
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
