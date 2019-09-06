<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Database\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use LoyaltyCorp\Multitenancy\Database\Interfaces\HasProviderInterface;
use LoyaltyCorp\Multitenancy\Database\Traits\HasProvider;

/**
 * This stub entity is to test HasProvider trait.
 *
 * @ORM\Entity()
 */
final class EntityHasProviderStub implements HasProviderInterface
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
     * @ORM\Column(type="string", nullable=false, unique=true)
     *
     * @var string The immutable external ID.
     */
    private $externalId;

    /**
     * @ORM\Column(type="string", nullable=false)
     *
     * @var string
     */
    private $name;

    /**
     * Collection of owned entities
     *
     * @ORM\OneToMany(
     *     cascade={"persist"},
     *     mappedBy="owner",
     *     targetEntity="Tests\LoyaltyCorp\Multitenancy\Stubs\Database\Entities\EntityHasProviderStub"
     * )
     *
     * @var \Doctrine\Common\Collections\Collection
     */
    private $owned;

    /**
     * Owner relationship, inverse of owned collection
     *
     * @ORM\ManyToOne(
     *     inversedBy="owned",
     *     targetEntity="Tests\LoyaltyCorp\Multitenancy\Stubs\Database\Entities\EntityHasProviderStub"
     * )
     *
     * @var \Tests\LoyaltyCorp\Multitenancy\Stubs\Database\Entities\EntityHasProviderStub
     */
    private $owner;

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

        $this->owned = new ArrayCollection();
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

    /**
     * Get external id set in constructor
     *
     * @return string
     */
    public function getExternalId(): string
    {
        return $this->externalId;
    }

    /**
     * Get owned collection
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOwned(): Collection
    {
        return $this->owned;
    }

    /**
     * Set entity id
     *
     * @param string $entityId The id to set against the entity
     *
     * @return mixed
     */
    public function setEntityId(string $entityId)
    {
        $this->entityId = $entityId;

        return $this;
    }

    /**
     * Set entity owner
     *
     * @param \Tests\LoyaltyCorp\Multitenancy\Stubs\Database\Entities\EntityHasProviderStub $owner
     *
     * @return void
     */
    public function setOwner(EntityHasProviderStub $owner): void
    {
        $this->owner = $owner;
    }
}
