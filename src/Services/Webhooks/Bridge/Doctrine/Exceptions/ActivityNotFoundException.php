<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Services\Webhooks\Bridge\Doctrine\Exceptions;

use EoneoPay\Webhooks\Exceptions\WebhooksException;
use RuntimeException;

final class ActivityNotFoundException extends RuntimeException implements WebhooksException
{
}
