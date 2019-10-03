<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Services\Requests;

use Doctrine\Common\Persistence\ManagerRegistry;
use EoneoPay\Externals\ORM\Interfaces\RepositoryInterface as GenericRepositoryInterface;
use LoyaltyCorp\Multitenancy\Database\Entities\Provider;
use LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\RepositoryInterface;
use LoyaltyCorp\Multitenancy\Services\Requests\Exceptions\InvalidProviderException;
use LoyaltyCorp\RequestHandlers\Serializer\Interfaces\DoctrineDenormalizerEntityFinderInterface;

final class ProviderAwareEntityFinder implements DoctrineDenormalizerEntityFinderInterface
{
    /**
     * @var \Doctrine\Common\Persistence\ManagerRegistry
     */
    private $entityManager;

    /**
     * Create entity finder.
     *
     * @param \Doctrine\Common\Persistence\ManagerRegistry $entityManager
     */
    public function __construct(ManagerRegistry $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LoyaltyCorp\Multitenancy\Services\Requests\Exceptions\InvalidProviderException
     */
    public function findOneBy(string $class, array $criteria, ?array $context = null): ?object
    {
        $repository = $this->entityManager->getRepository($class);

        if (($repository instanceof RepositoryInterface) === true) {
            /**
             * @var \LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\RepositoryInterface $repository
             *
             * @see https://youtrack.jetbrains.com/issue/WI-37859 - typehint required until PhpStorm recognises === chek
             */

            // Look up the provider inside the context.
            $provider = \is_array($context) === true
                ? ($context[RequestBodyContextConfigurator::MULTITENANCY_PROVIDER] ?? null)
                : null;

            // If we dont have a provider we cannot continue.
            if ($provider instanceof Provider === false) {
                throw new InvalidProviderException(
                    'A provider was not found in context when deserialising.'
                );
            }

            return $repository->findOneBy($provider, $criteria);
        }

        if (($repository instanceof GenericRepositoryInterface) === true) {
            /**
             * @var \EoneoPay\Externals\ORM\Interfaces\RepositoryInterface $repository
             *
             * @see https://youtrack.jetbrains.com/issue/WI-37859 - typehint required until PhpStorm recognises === chek
             */
            return $repository->findOneBy($criteria);
        }

        // The repository type is unknown or not part of our application
        return null;
    }
}
