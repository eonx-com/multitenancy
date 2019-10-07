<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Services\Webhooks\Bridge\Doctrine\Entities\Schemas;

use Tests\LoyaltyCorp\Multitenancy\Stubs\Services\Webhooks\Bridge\Doctrine\Entities\ProviderAwareActivityStub;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\External\ORM\EntityStub;
use Tests\LoyaltyCorp\Multitenancy\TestCases\AppTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Entities\Schemas\ProviderAwareActivitySchema
 */
final class ProviderAwareActivitySchemaTest extends AppTestCase
{
    /**
     * Tests setPrimaryEntity.
     *
     * @return void
     */
    public function testSetPrimaryEntity(): void
    {
        $schema = new ProviderAwareActivityStub();
        $schema->setPrimaryEntity(new EntityStub());

        self::assertSame(EntityStub::class, $schema->getPrimaryClass());
        self::assertSame('1', $schema->getPrimaryId());
    }
}
