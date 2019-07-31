<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\External\Auth;

use EoneoPay\Externals\Auth\Interfaces\AuthInterface;
use EoneoPay\Externals\ORM\Interfaces\EntityInterface;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\External\ORM\UserStub;

/**
 * @coversNothing
 */
class AuthStub implements AuthInterface
{
    /**
     * @var \Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\External\ORM\UserStub|null
     */
    private $user;

    /**
     * AuthStub constructor.
     *
     * @param \Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\External\ORM\UserStub|null $user
     */
    public function __construct(?UserStub $user)
    {
        $this->user = $user;
    }

    /**
     * {@inheritdoc}
     */
    public function user(): ?EntityInterface
    {
        return $this->user;
    }
}
