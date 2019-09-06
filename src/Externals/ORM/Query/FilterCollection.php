<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Externals\ORM\Query;

use Doctrine\ORM\Query\FilterCollection as DoctrineFilterCollection;
use InvalidArgumentException;
use LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM\Query\FilterCollectionInterface;
use LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\ORMException;

final class FilterCollection implements FilterCollectionInterface
{
    /**
     * @var \Doctrine\ORM\Query\FilterCollection
     */
    private $collection;

    /**
     * Create a new filter collection from a Doctrine FilterCollection.
     *
     * @param \Doctrine\ORM\Query\FilterCollection $filterCollection
     */
    public function __construct(DoctrineFilterCollection $filterCollection)
    {
        $this->collection = $filterCollection;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\ORMException If the filter does not exist
     */
    public function disable($name): void
    {
        $this->callMethod('disable', $name);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\ORMException If the filter does not exist
     */
    public function enable($name): void
    {
        $this->callMethod('enable', $name);
    }

    /**
     * Call a method on the entity manager and catch any exception.
     *
     * @param string $method The method to call
     * @param mixed ...$parameters The parameters to pass to the method
     *
     * @return mixed
     *
     * @throws \LoyaltyCorp\Multitenancy\Externals\ORM\Exceptions\ORMException If the filter does not exist
     */
    private function callMethod(string $method, ...$parameters)
    {
        try {
            $callable = [$this->collection, $method];

            if (\is_callable($callable)) {
                return \call_user_func_array($callable, $parameters ?? []);
            }
        } catch (InvalidArgumentException $exception) {
            // Wrap exceptions in ORMException
            throw new ORMException(\sprintf('Database Error: %s', $exception->getMessage()), null, null, $exception);
        }

        // Something has gone massively wrong, this should not really be possible since method is private
        throw new ORMException(\sprintf('Invalid method called: %s()', $method)); // @codeCoverageIgnore
    }
}
