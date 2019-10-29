<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Unit\Bridge\Laravel\Console\Commands;

use LoyaltyCorp\Multitenancy\Bridge\Laravel\Console\Commands\CreateProviderCommand;
use ReflectionClass;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Output\BufferedOutput;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Services\Providers\ProviderServiceStub;
use Tests\LoyaltyCorp\Multitenancy\TestCases\Unit\CommandTestCase;

/**
 * @covers \LoyaltyCorp\Multitenancy\Bridge\Laravel\Console\Commands\CreateProviderCommand
 */
final class CreateProviderCommandTest extends CommandTestCase
{
    /**
     * Test the create provider command evoking an error.
     *
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testErrorOutput(): void
    {
        $command = $this->createInstance([]);
        $command->handle(new ProviderServiceStub());

        /** @var \Symfony\Component\Console\Output\BufferedOutput $output */
        $output = $command->getOutput();
        $text = $output->fetch();

        self::assertStringContainsString('Provided inputs were not expected format', $text);
    }

    /**
     * Test the creation of provider under successful circumstances.
     *
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testSuccessfulOutput(): void
    {
        $command = $this->createInstance([
            'identifier' => 'test-provider',
            'name' => 'Test Provider',
        ]);
        /** @var \Symfony\Component\Console\Output\BufferedOutput $output */
        $output = $command->getOutput();

        $command->handle(new ProviderServiceStub());
        $text = $output->fetch();

        self::assertStringContainsString('Provider has been created.', $text);
        // ProviderServiceStub yields the 'test-provider' details
        self::assertStringContainsString('test-provider | Test Provider', $text);
    }

    /**
     * Create an instance of the command.
     *
     * @param string[]|null $input
     *
     * @return \LoyaltyCorp\Multitenancy\Bridge\Laravel\Console\Commands\CreateProviderCommand
     *
     * @throws \ReflectionException
     */
    private function createInstance(?array $input = null): CreateProviderCommand
    {
        // Use reflection to access input property as it is protected
        // and derived from the application/console input
        $class = new ReflectionClass(CreateProviderCommand::class);
        $inputProperty = $class->getProperty('input');
        $outputProperty = $class->getProperty('output');

        // Set property to public
        $inputProperty->setAccessible(true);
        $outputProperty->setAccessible(true);

        // Create instance
        $instance = new CreateProviderCommand();

        // Set input property options
        $inputProperty->setValue($instance, new ArrayInput(
            $input ?? [
                'identifier' => '',
                'name' => '',
            ],
            new InputDefinition([
                new InputArgument('identifier'),
                new InputArgument('name'),
            ])
        ));
        $outputProperty->setValue($instance, new BufferedOutput());

        return $instance;
    }
}
