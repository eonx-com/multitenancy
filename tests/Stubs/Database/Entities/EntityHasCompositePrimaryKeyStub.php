<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Database\Entities;

use Doctrine\ORM\Mapping as ORM;
use LoyaltyCorp\Multitenancy\Database\Interfaces\HasProviderInterface;
use LoyaltyCorp\Multitenancy\Database\Traits\HasProvider;

/**
 * This stub has composite primary keys.
 *
 * @ORM\Entity(repositoryClass="\Tests\LoyaltyCorp\Multitenancy\Stubs\Database\RepositoryStub")
 */
final class EntityHasCompositePrimaryKeyStub implements HasProviderInterface
{
    use HasProvider;

    /**
     * @ORM\Column(type="string", nullable=false, unique=true)
     * @ORM\Id()
     *
     * @var string The immutable external ID.
     */
    private $externalId;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @ORM\Id()
     *
     * @var string
     */
    private $name;

    /**
     * EntityHasProviderStub constructor.
     *
     * @param string $externalId
     * @param string $name
     */
    public function __construct(string $externalId, string $name)
    {
        $this->externalId = $externalId;
        $this->name = $name;
    }
}
