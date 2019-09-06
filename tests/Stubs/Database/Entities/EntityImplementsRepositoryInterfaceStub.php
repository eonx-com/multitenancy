<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Database\Entities;

use Doctrine\ORM\Mapping as ORM;
use LoyaltyCorp\Multitenancy\Database\Interfaces\HasProviderInterface;
use LoyaltyCorp\Multitenancy\Database\Traits\HasProvider;

/**
 * This stub has a repository with the right interface.
 *
 * @ORM\Entity(repositoryClass="\Tests\LoyaltyCorp\Multitenancy\Stubs\Database\RepositoryStub")
 */
final class EntityImplementsRepositoryInterfaceStub implements HasProviderInterface
{
    use HasProvider;

    /**
     * @ORM\Column(type="string", name="id")
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Id()
     *
     * @var string Internal Database ID.
     */
    private $entityId;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $string;

    /**
     * Create entity stub
     *
     * @param string $string
     */
    public function __construct(string $string)
    {
        $this->string = $string;
    }

    /**
     * Get entity id
     *
     * @return string|null
     */
    public function getEntityId(): ?string
    {
        return $this->entityId;
    }
}
