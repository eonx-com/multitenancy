<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Services\Webhooks\Bridge\Doctrine\Handlers;

use Doctrine\Instantiator\Exception\ExceptionInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Exception;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Exceptions\DoctrineMisconfiguredException;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Exceptions\EntityNotCreatedException;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Handlers\ActivityHandler;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Models\ProviderAwareActivityInterface;
use stdClass;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Externals\ORM\EntityManagerStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Services\Webhooks\Bridge\Doctrine\Entities\ProviderAwareActivityStub;
use Tests\LoyaltyCorp\Multitenancy\TestCases\AppTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Handlers\ActivityHandler
 */
final class ActivityHandlerTest extends AppTestCase
{
    /**
     * Create new fails.
     *
     * @return void
     */
    public function testCreateFails(): void
    {
        $this->expectException(EntityNotCreatedException::class);
        $this->expectExceptionMessage(\sprintf(
            'An error occurred creating an %s instance.',
            ProviderAwareActivityInterface::class
        ));

        $classMetadata = $this->createMock(ClassMetadata::class);
        $classMetadata->expects(self::once())
            ->method('newInstance')
            ->willThrowException(new class() extends Exception implements ExceptionInterface {
            });

        $requestHandler = $this->createInstance($classMetadata);
        $requestHandler->create();
    }

    /**
     * Create new webhook from interface.
     *
     * @return void
     */
    public function testCreateNew(): void
    {
        $requestHandler = $this->createInstance();
        $request = $requestHandler->create();

        self::assertInstanceOf(ProviderAwareActivityStub::class, $request);
    }

    /**
     * Tests get failure.
     *
     * @return void
     */
    public function testGetFailure(): void
    {
        $provider = new Provider('provider', 'Provider Inc.');
        $requestHandler = $this->createInstance(null, new stdClass());

        $this->expectException(DoctrineMisconfiguredException::class);

        $requestHandler->get($provider, 5);
    }

    /**
     * Tests get success.
     *
     * @return void
     */
    public function testGetSuccess(): void
    {
        $provider = new Provider('provider', 'Provider Inc.');
        $activity = new ProviderAwareActivityStub();

        $requestHandler = $this->createInstance(null, $activity);
        $result = $requestHandler->get($provider, 5);

        self::assertSame($activity, $result);
    }

    /**
     * Tests get success.
     *
     * @return void
     */
    public function testGetSuccessNull(): void
    {
        $provider = new Provider('provider', 'Provider Inc.');
        $requestHandler = $this->createInstance();
        $result = $requestHandler->get($provider, 5);

        self::assertNull($result);
    }

    /**
     * Save.
     *
     * @return void
     */
    public function testSave(): void
    {
        $provider = new Provider('provider', 'Provider Inc.');
        $requestHandler = $this->createInstance();
        $requestHandler->save($provider, new ProviderAwareActivityStub());

        // If no exception is thrown it's all good
        $this->addToAssertionCount(1);
    }

    /**
     * Create handler instance.
     *
     * @param \Doctrine\ORM\Mapping\ClassMetadata $classMetadata
     * @param mixed $activity
     *
     * @return \LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Handlers\ActivityHandler
     */
    private function createInstance(
        ?ClassMetadata $classMetadata = null,
        $activity = null
    ): ActivityHandler {
        $classMetadata = $classMetadata ?? new ClassMetadata(ProviderAwareActivityStub::class);

        return new ActivityHandler(new EntityManagerStub(
            $activity,
            [ProviderAwareActivityInterface::class => $classMetadata]
        ));
    }
}
