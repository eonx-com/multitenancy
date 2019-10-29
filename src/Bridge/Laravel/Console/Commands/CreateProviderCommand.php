<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Bridge\Laravel\Console\Commands;

use Illuminate\Console\Command;
use LoyaltyCorp\Multitenancy\Services\Providers\Interfaces\ProviderServiceInterface;

final class CreateProviderCommand extends Command
{
    /**
     * Constructor for creation of a provider command.
     */
    public function __construct()
    {
        $this->description = 'Create a new provider';
        $this->signature = 'app:provider:create {identifier} {name}';

        parent::__construct();
    }

    /**
     * Extract exception codes.
     *
     * @param \LoyaltyCorp\Multitenancy\Services\Providers\Interfaces\ProviderServiceInterface $providerService
     *
     * @return void
     */
    public function handle(ProviderServiceInterface $providerService): void
    {
        [$identifier, $name] = [$this->argument('identifier'), $this->argument('name')];

        if (\is_string($identifier) === false || \is_string($name) === false) {
            $this->error('Provided inputs were not expected format');

            return;
        }

        $provider = $providerService->create($identifier, $name);

        $this->info('Provider has been created.');
        $this->table(
            ['ID', 'External ID', 'Name', ],
            [
                [
                    $provider->getProviderId(),
                    $provider->getExternalId(),
                    $provider->getName(),
                ],
            ]
        );
    }
}
