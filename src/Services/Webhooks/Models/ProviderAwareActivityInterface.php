<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Services\Webhooks\Models;

use EoneoPay\Webhooks\Models\ActivityInterface;
use LoyaltyCorp\Multitenancy\Database\Interfaces\HasProviderInterface;

interface ProviderAwareActivityInterface extends ActivityInterface, HasProviderInterface
{
}
