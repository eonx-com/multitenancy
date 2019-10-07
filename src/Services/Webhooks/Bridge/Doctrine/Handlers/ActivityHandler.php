<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Handlers;

use Doctrine\Instantiator\Exception\ExceptionInterface;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\EntityManagerInterface;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Exceptions\DoctrineMisconfiguredException;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Exceptions\EntityNotCreatedException;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Handlers\Interfaces\ActivityHandlerInterface;
use LoyaltyCorp\Multitenancy\Services\Webhooks\Model\ProviderAwareActivityInterface;

final class ActivityHandler implements ActivityHandlerInterface
{
    /**
     * @var \LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * Constructor.
     *
     * @param \LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Exceptions\EntityNotCreatedException
     */
    public function create(): ProviderAwareActivityInterface
    {
        try {
            /**
             * @var \LoyaltyCorp\Multitenancy\Services\Webhooks\Model\ProviderAwareActivityInterface $instance
             */
            $instance = $this->entityManager->getClassMetadata(ProviderAwareActivityInterface::class)->newInstance();
        } /** @noinspection PhpRedundantCatchClauseInspection */ catch (ExceptionInterface $exception) {
            throw new EntityNotCreatedException(
                \sprintf(
                    'An error occurred creating an %s instance.',
                    ProviderAwareActivityInterface::class
                ),
                0,
                $exception
            );
        }

        return $instance;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Exceptions\DoctrineMisconfiguredException
     */
    public function get(Provider $provider, int $activityId): ?ProviderAwareActivityInterface
    {
        $activity = $this->entityManager->find($provider, ProviderAwareActivityInterface::class, $activityId);

        if ($activity !== null && ($activity instanceof ProviderAwareActivityInterface) === false) {
            throw new DoctrineMisconfiguredException(\sprintf(
                'When querying for a "%s" object, Doctrine returned "%s"',
                ProviderAwareActivityInterface::class,
                \get_class($activity)
            ));
        }

        return $activity;
    }

    /**
     * {@inheritdoc}
     */
    public function save(Provider $provider, ProviderAwareActivityInterface $activity): void
    {
        $this->entityManager->persist($provider, $activity);
        $this->entityManager->flush($provider);
    }
}
