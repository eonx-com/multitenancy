<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\TestCases\Unit;

use Doctrine\ORM\Mapping\Id;
use EoneoPay\Externals\ORM\Interfaces\EntityInterface as BaseEntityInterface;
use EoneoPay\Utils\AnnotationReader;
use EoneoPay\Utils\Interfaces\SerializableInterface;
use ReflectionClass;
use Tests\LoyaltyCorp\Multitenancy\DoctrineTestCase;

abstract class EntityTestCase extends DoctrineTestCase
{
    /**
     * Test identifier.
     *
     * @return void
     *
     * @throws \EoneoPay\Utils\Exceptions\AnnotationCacheException If opcache isn't caching annotations
     * @throws \ReflectionException If entity isn't the right class
     */
    public function testIdentifier(): void
    {
        // Schema must be created for annotations to be read correctly
        $this->createSchema();

        $entityClass = $this->getEntityClass();

        // Get id property
        $ids = (new AnnotationReader())->getClassPropertyAnnotation($entityClass, Id::class);
        $entityId = \key($ids);

        // Ensure there is an id
        self::assertNotNull($entityId);

        // Create entity
        $entity = $this->getEntityInstance();

        // Make getIdProperty available to ensure it matches
        $class = new ReflectionClass($entityClass);
        $method = $class->getMethod('getIdProperty');
        $method->setAccessible(true);

        self::assertSame($entityId, $method->invoke($entity));
    }

    /**
     * Entity should return array with good keys.
     *
     * @return void
     */
    public function testToArray(): void
    {
        /** @var \EoneoPay\Utils\Interfaces\SerializableInterface|null $entity */
        $entity = $this->getEntityInstance();
        self::assertInstanceOf(SerializableInterface::class, $entity);

        $data = $entity->toArray();

        $this->assertArrayHasKeys($this->getToArrayKeys(), $data);

        $keys = \array_keys($data);
        $keysWithUppercase = \array_filter($keys, static function ($key): bool {
            return \preg_match('/([A-Z])/', $key) > 0;
        });

        static::assertCount(0, $keysWithUppercase, 'toArray keys must not contain uppercase letters');
    }

    /**
     * Get entity class name.
     *
     * @return string
     */
    abstract protected function getEntityClass(): string;

    /**
     * Gets an instance of an entity.
     *
     * @return \EoneoPay\Externals\ORM\Interfaces\EntityInterface
     */
    abstract protected function getEntityInstance(): BaseEntityInterface;

    /**
     * Get keys returned with to array.
     *
     * @return string[]
     */
    abstract protected function getToArrayKeys(): array;

    /**
     * Assert given array has all given keys.
     *
     * @param string[] $keys
     * @param mixed[] $array
     *
     * @return void
     */
    protected function assertArrayHasKeys(array $keys, array $array): void
    {
        // Add timestamps
        $keys = \array_unique($keys);

        // Sort arrays
        \sort($keys);
        \ksort($array);

        self::assertSame($keys, \array_keys($array));
    }
}
