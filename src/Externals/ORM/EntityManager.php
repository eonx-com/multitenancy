<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Externals\ORM;

use Doctrine\ORM\EntityManagerInterface as DoctrineEntityManager;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\EntityManagerInterface;
use LoyaltyCorp\Multitenancy\Externals\ORM\Subscribers\ProtectedFlushSubscriber;

final class EntityManager implements EntityManagerInterface
{
    /**
     * Doctrine entity manager
     *
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * Create an internal entity manager
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    public function __construct(DoctrineEntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function flush(Provider $provider): void
    {
        $eventManager = $this->entityManager->getEventManager();

        // Add subscriber which will check the entities
        $protectedFlush = new ProtectedFlushSubscriber($provider);
        $eventManager->addEventSubscriber($protectedFlush);

        // Attempt to perform the flush
        $this->entityManager->flush();

        // Clean up by removing the subscriber
        $eventManager->removeEventSubscriber($protectedFlush);
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository(string $class)
    {
        return $this->entityManager->getRepository($class);
    }

    /**
     * {@inheritdoc}
     */
    public function persist($entity): void
    {
        $this->entityManager->persist($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($entity): void
    {
        $this->entityManager->remove($entity);
    }
}
