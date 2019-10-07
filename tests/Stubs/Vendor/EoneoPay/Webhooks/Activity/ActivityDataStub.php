<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\EoneoPay\Webhooks\Activity;

use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
use EoneoPay\Webhooks\Activities\Interfaces\ActivityDataInterface;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\External\ORM\EntityStub;

/**
 * @coversNothing
 */
final class ActivityDataStub implements ActivityDataInterface
{
    /**
     * @var \EoneoPay\Externals\ORM\Interfaces\EntityInterface
     */
    private $entity;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->entity = new EntityStub();
    }

    /**
     * {@inheritdoc}
     */
    public static function getActivityKey(): string
    {
        return 'activity.constant';
    }

    /**
     * {@inheritdoc}
     */
    public function getPrimaryEntity(): EntityInterface
    {
        return $this->entity;
    }
}
