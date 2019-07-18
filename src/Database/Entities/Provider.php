<?php
declare(strict_types=1);

namespace LoyaltyCorp\Mulitenancy\Database\Entities;

use Doctrine\ORM\Mapping as ORM;
use EoneoPay\Framework\Database\Entities\Entity;

/**
 * Provider represents a customer of Loyalty Corp in this product.
 *
 * @ORM\Entity()
 *
 * @SuppressWarnings(PHPMD.UnusedPrivateField) Suppress warning about "unused" $tenantId.
 */
class Provider extends Entity
{
    /**
     * @ORM\Column(type="string", nullable=false, unique=true)
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
     * Get the External ID of the tenant. This is generated by an external system (usually ManageV2).
     *
     * @return string
     */
    public function getExternalId(): string
    {
        return $this->externalId;
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

    /**
     * Serialize entity as an array
     *
     * @return mixed[]
     */
    public function toArray(): array
    {
        return [
            'externalId' => $this->getId(),
            'name' => $this->getName()
        ];
    }

    /**
     * Get the id property for this entity
     *
     * @return string
     */
    protected function getIdProperty(): string
    {
        return 'externalId';
    }
}
