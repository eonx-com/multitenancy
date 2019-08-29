<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Database\Traits;

use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Database\Traits\HasProvider;
use Tests\LoyaltyCorp\Multitenancy\TestCases\AppTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Database\Traits\HasProvider
 */
class HasProviderTest extends AppTestCase
{
    /**
     * Tests that the getter returns the same provider instance as what was set through the setter.
     *
     * @return void
     */
    public function testTraitGetterSetter(): void
    {
        $provider = new Provider('test-provider', 'Test Provider');
        $dummy = new class
        {
            use HasProvider;
        };
        $dummy->setProvider($provider);

        self::assertSame($provider, $dummy->getProvider());
    }
}
