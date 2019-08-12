<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\TestCases\Unit;

use Doctrine\Common\Persistence\Mapping\Driver\DefaultFileLocator;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Illuminate\Support\ServiceProvider;
use ReflectionClass;
use Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\Laravel\Lumen\ApplicationStub;
use Tests\LoyaltyCorp\Multitenancy\TestCases\AppTestCase;

/**
 * @coversNothing
 */
abstract class ServiceProviderTestCase extends AppTestCase
{
    /**
     * Test container bindings from the register method.
     *
     * @return void
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException If container doesn't contain key
     * @throws \ReflectionException If reflected class doesn't exist
     * @throws \Doctrine\ORM\ORMException
     */
    public function testBindings(): void
    {
        $application = new ApplicationStub($this->app);
        $entityManager = $this->app->make('registry')->getManager();
        $this->setupDoctrineDrivers($entityManager);

        // Create the service provider, register bindings and check they've been specified
        $class = $this->getServiceProvider();
        $provider = new $class($application);

        // Ensure this is a service provider
        /** @var \Illuminate\Support\ServiceProvider|mixed $provider */
        self::assertInstanceOf(ServiceProvider::class, $provider);

        // Register
        $provider->register();

        // Check service provider doesn't bind more than what is tested
        $abstracts = \array_keys($this->getBindings());
        \sort($abstracts);
        $bindings = $application->getBindings();
        \sort($bindings);
        self::assertSame($abstracts, $bindings);

        // Test outcomes
        foreach ($this->getBindings() as $abstract => $concrete) {
            self::assertInstanceOf($concrete, $application->make($abstract));

            // If we're abstracting a class, make sure the implementation extends the class
            if (\class_exists($abstract) === true) {
                $reflected = new ReflectionClass($abstract);

                // Ignore facades bound to strings
                if (\strncmp($reflected->name, 'Illuminate\\Support\\Facades', 26) === 0) {
                    continue;
                }

                self::assertInstanceOf($abstract, $application->make($abstract));
            }

            // If we're abstracting an interface, make sure concrete implements interface
            if (\interface_exists($abstract) === true) {
                self::assertInstanceOf($abstract, $application->make($abstract));
            }
        }
    }

    /**
     * Get expected bindings from the container.
     *
     * @return string[]
     */
    abstract protected function getBindings(): array;

    /**
     * Get service provider class.
     *
     * @return string
     */
    abstract protected function getServiceProvider(): string;

    /**
     * Add XML driver to doctrine for flow config.
     *
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     *
     * @return void
     *
     * @throws \Doctrine\ORM\ORMException
     */
    private function setupDoctrineDrivers(EntityManagerInterface $entityManager): void
    {
        $chainDriver = $entityManager->getConfiguration()->getMetadataDriverImpl();

        if ($chainDriver instanceof MappingDriverChain === false) {
            return;
        }

        $path = \sprintf('%s/vendor/code-foundation/flow-config/src/Entity/DoctrineMaps/', $this->app->basePath());
        $xmlDriver = new XmlDriver(
            new DefaultFileLocator($path, '.orm.xml')
        );

        /**
         * @var \Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain $chainDriver
         */
        $chainDriver->addDriver($xmlDriver, 'CodeFoundation\FlowConfig');
    }
}
