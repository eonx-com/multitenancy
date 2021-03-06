<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Database\Entities;

use Doctrine\ORM\Mapping as ORM;
use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
use LoyaltyCorp\FlowConfig\Database\Interfaces\FlowConfigurableInterface;

/**
 * Provider represents a customer of Loyalty Corp in this product.
 *
 * @ORM\Entity()
 * @ORM\Table(name="multitenancy_provider")
 */
class Provider implements EntityInterface, FlowConfigurableInterface
{
    /**
     * @ORM\Column(type="string", length=100, nullable=false, unique=true)
     *
     * @var string The immutable external ID.
     */
    private $externalId;

    /**
     * @ORM\Column(type="string", nullable=false)
     *
     * @var string Common name of provider.
     */
    private $name;

    /**
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Id()
     *
     * @var int Internal Database ID.
     */
    private $providerId;

    /**
     * Tenant constructor.
     *
     * @param string $externalId External ID to find this tenant by.
     * @param string $name Common name of the tenant.
     */
    public function __construct(string $externalId, string $name)
    {
        $this->externalId = $externalId;
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityId(): string
    {
        return (string)$this->providerId;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityType(): string
    {
        return 'multi_tenancy_provider';
    }

    /**
     * Get the External ID of the tenant. This is generated by an external system (usually ManageV2).
     *
     * @return string
     */
    public function getExternalId(): string
    {
        return $this->externalId;
    }

    /**
     * {@inheritdoc}
     */
    public function getId(): ?int
    {
        return $this->providerId;
    }

    /**
     * Get the common name of the tenant. eg; "LoyaltyCorp", "RACQ", or "Optus".
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get provider id.
     *
     * @return int|null
     */
    public function getProviderId(): ?int
    {
        return $this->providerId;
    }

    /**
     * Update common name of the tenant.
     *
     * @param string $name Name to change on tenant.
     *
     * @return void
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }
}
