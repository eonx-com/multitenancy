<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Bridge\Laravel\Console\Commands;

use EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface;
use Illuminate\Console\Command;
use LoyaltyCorp\Multitenancy\Bridge\Laravel\Console\Exceptions\CommandOptionUnusable;
use LoyaltyCorp\Multitenancy\Services\Providers\Interfaces\ProviderServiceInterface;

final class CreateProviderCommand extends Command
{
    /**
     * Constructor for creation of a provider command.
     */
    public function __construct()
    {
        $this->description = 'Create a new provider';
        $this->signature = 'app:provider:create 
        {--identifier: The unique identifier for the provider to be referenced by} 
        {--name: Human friendly name of the provider}';

        parent::__construct();
    }

    /**
     * Extract exception codes.
     *
     * @param \EoneoPay\Externals\ORM\Interfaces\EntityManagerInterface $entityManager
     * @param \LoyaltyCorp\Multitenancy\Services\Providers\Interfaces\ProviderServiceInterface $providerService
     *
     * @return void
     *
     * @throws \LoyaltyCorp\Multitenancy\Bridge\Laravel\Console\Exceptions\CommandOptionUnusable
     */
    public function handle(EntityManagerInterface $entityManager, ProviderServiceInterface $providerService): void
    {
        [$identifier, $name] = [$this->getOptionValue('identifier'), $this->getOptionValue('name')];

        $provider = $providerService->create($identifier, $name);

        $entityManager->flush();

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

    /**
     * Resolve a supplied option to the string value.
     *
     * @param string $key
     *
     * @return string
     *
     * @throws \LoyaltyCorp\Multitenancy\Bridge\Laravel\Console\Exceptions\CommandOptionUnusable
     */
    private function getOptionValue(string $key): string
    {
        $value = $this->option($key);

        if (\is_array($value) === true) {
            // Try and resolve some usable value from the 'unexpected' array
            $value = \reset($value);
        }

        if (\is_string($value) === true) {
            return $value;
        }

        throw new CommandOptionUnusable(\sprintf('Option \'%s\' could not be resolved to a usable value', $key));
    }
}
