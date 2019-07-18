<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Http\Controllers;

use EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface;
use LoyaltyCorp\Mulitenancy\Database\Entities\Provider;
use LoyaltyCorp\Mulitenancy\Http\Controllers\ProviderController;
use LoyaltyCorp\Mulitenancy\Http\Requests\Providers\ProviderCreateRequest;
use LoyaltyCorp\Mulitenancy\Http\Requests\Providers\ProviderModifyRequest;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Externals\Database\EntityManagerSpy;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Externals\Database\EntityManagerStub;
use Tests\LoyaltyCorp\Multitenancy\TestCases\Unit\ControllerTestCase;

class ProviderControllerTest extends ControllerTestCase
{
    /**
     * Tests that the `create` method on the controller is successful.
     *
     * @return void
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function testProviderCreateSuccess(): void
    {
        $provider = new Provider('test-provider', 'Test Provider');
        $entityManager = new EntityManagerSpy();
        $controller = $this->getControllerInstance($entityManager);
        $request = $this->buildUnvalidatedRequestObject(
            ProviderCreateRequest::class,
            ['id' => 'test-provider', 'name' => 'Test Provider']
        );

        /**
         * @var \LoyaltyCorp\Mulitenancy\Http\Requests\Providers\ProviderCreateRequest $request
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
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function testProviderModifySuccess(): void
    {
        $entityManager = $this->getEntityManager();
        $provider = new Provider('test-provider', 'Test Provider');
        $entityManager->persist($provider);
        $entityManager->flush();
        $controller = $this->getControllerInstance($entityManager);
        $request = $this->buildUnvalidatedRequestObject(
            ProviderModifyRequest::class,
            ['name' => 'Something Different']
        );

        /**
         * @var \LoyaltyCorp\Mulitenancy\Http\Requests\Providers\ProviderModifyRequest $request
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
     *
     * @return \LoyaltyCorp\Mulitenancy\Http\Controllers\ProviderController
     */
    private function getControllerInstance(?EntityManagerInterface $entityManager = null): ProviderController
    {
        return new ProviderController(
            $entityManager ?? new EntityManagerStub()
        );
    }
}
