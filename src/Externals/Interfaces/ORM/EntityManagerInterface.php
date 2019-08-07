<?php
declare(strict_types=1);

namespace LoyaltyCorp\Multitenancy\Externals\Interfaces\ORM;

use LoyaltyCorp\Multitenancy\Database\Entities\Provider;

interface EntityManagerInterface
{
    /**
     * Flush unit of work to the database, ensuring all entities belong to the correct provider if applicable
     *
     * @param \LoyaltyCorp\Multitenancy\Database\Entities\Provider $provider Provider who should own all entities in UOW
     *
     * @return void
     */
    public function flush(Provider $provider): void;

    /**
     * Gets the repository from a entity class
     *
     * @param string $class The class name of the entity to generate a repository for
     *
     * @return mixed The instantiated repository
     */
    public function getRepository(string $class);

    /**
     * Persist entity to the database
     *
     * @param object $entity The entity to persist to the database
     *
     * @return void
     */
    public function persist(object $entity): void;

    /**
     * Remove entity from the database.
     *
     * @param object $entity The entity to remove from the database
     *
     * @return void
     */
    public function remove(object $entity): void;
}
