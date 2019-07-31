<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Database\Seeders;

use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Database\Seeders\ProviderSeeder;
use PHPUnit\Framework\TestCase;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\Doctrine\EntityManagerStub;

/**
 * @covers \LoyaltyCorp\Multitenancy\Database\Seeders\ProviderSeeder
 */
class ProviderSeederTest extends TestCase
{
    /**
     * Tests that the Provider Seeder works as expected.
     *
     * @return void
     */
    public function testSeed(): void
    {
        $expected = [
            new Provider('loyalty-corp', 'Loyalty Corp'),
            new Provider('moon-corp', 'Mooncorp')
        ];

        $entityManager = new EntityManagerStub();
        $seeder = new ProviderSeeder($entityManager);

        $seeder->seed();

        static::assertEquals($expected, $entityManager->getPersisted());
        static::assertSame(1, $entityManager->getFlushCount());
    }
}
