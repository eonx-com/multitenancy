<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Database\Seeders;

use Doctrine\ORM\EntityManagerInterface;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;

class ProviderSeeder
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * Constructor
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Creates multiple example providers.
     *
     * @return void
     */
    public function seed(): void
    {
        $provider = new Provider('loyalty-corp', 'Loyalty Corp');
        $this->entityManager->persist($provider);

        $provider = new Provider('moon-corp', 'Mooncorp');
        $this->entityManager->persist($provider);

        $this->entityManager->flush();
    }
}
