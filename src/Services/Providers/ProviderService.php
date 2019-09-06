<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Services\Providers;

use EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Services\Providers\Interfaces\ProviderServiceInterface;

final class ProviderService implements ProviderServiceInterface
{
    /**
     * @var \EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface
     */
    private $entityManager;

    /**
     * Constructs a new instance of ProviderService.
     *
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $providerId, string $name): Provider
    {
        $provider = new Provider($providerId, $name);

        $this->entityManager->persist($provider);

        return $provider;
    }
}
