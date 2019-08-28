<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\External\ORM;

use EoneoPay\Externals\ORM\Interfaces\EntityInterface;

/**
 * @coversNothing
 */
class UserStub implements EntityInterface
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
    }
}
