<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\TestCases\Unit;

use LoyaltyCorp\RequestHandlers\Builder\Interfaces\ObjectBuilderInterface;
use LoyaltyCorp\RequestHandlers\TestHelper\RequestObjectTestHelper;
use Tests\LoyaltyCorp\Multitenancy\TestCases\AppTestCase;

/**
 * @coversNothing
 */
abstract class ControllerTestCase extends AppTestCase
{
    /**
     * Request object test helper - this is marked as @internal to ensure it's used for test purposes only.
     *
     * @var \LoyaltyCorp\RequestHandlers\TestHelper\RequestObjectTestHelper
     */
    protected $requestTestHelper;

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        /**
         * @noinspection PhpInternalEntityUsedInspection Ignore to provide full testing
         */
        $this->requestTestHelper = new RequestObjectTestHelper(
            $this->app->make(ObjectBuilderInterface::class),
            $this->app->make('requesthandlers_serializer')
        );
    }
}
