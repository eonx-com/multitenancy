<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Database\Seeders;

use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Database\Seeders\ProviderSeeder;
use PHPUnit\Framework\TestCase;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\Doctrine\ORM\EntityManagerStub;

/**
 * @covers \LoyaltyCorp\Multitenancy\Database\Seeders\ProviderSeeder
 */
final class ProviderSeederTest extends TestCase
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
            new Provider('moon-corp', 'Mooncorp'),
        ];

        $entityManager = new EntityManagerStub();
        $seeder = new ProviderSeeder($entityManager);

        $seeder->seed();

        self::assertEquals($expected, $entityManager->getPersisted());
        self::assertSame(1, $entityManager->getFlushCount());
    }
}
