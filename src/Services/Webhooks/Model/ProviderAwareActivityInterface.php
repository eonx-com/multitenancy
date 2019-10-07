<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Services\Webhooks\Model;

use EoneoPay\Webhooks\Model\ActivityInterface;
use LoyaltyCorp\Multitenancy\Database\Interfaces\HasProviderInterface;

interface ProviderAwareActivityInterface extends ActivityInterface, HasProviderInterface
{
}
