<?php
declare(strict_types=1);

namespace Tests\LoyaltyCorp\Multitenancy\Stubs\Vendor\Doctrine\Common;

use Doctrine\Common\EventManager;
use Doctrine\Common\EventSubscriber;

/**
 * @coversNothing
 */
class EventManagerStub extends EventManager
{
    /**
     * @var \Doctrine\Common\EventSubscriber[]
     */
    private $addedSubscribers = [];

    /**
     * @var \Doctrine\Common\EventSubscriber[]
     */
    private $removedSubscribers = [];

    /**
     * {@inheritdoc}
     */
    public function addEventSubscriber(EventSubscriber $subscriber): void
    {
        $this->addedSubscribers[] = $subscriber;

        parent::addEventSubscriber($subscriber);
    }

    /**
     * Get subscribers which have been added via addEventSubscriber
     *
     * @return \Doctrine\Common\EventSubscriber[]
     */
    public function getAddedSubscribers(): array
    {
        return $this->addedSubscribers;
    }

    /**
     * Get subscribers which have been removed via removeEventSubscriber
     *
     * @return \Doctrine\Common\EventSubscriber[]
     */
    public function getRemovedSubscribers(): array
    {
        return $this->removedSubscribers;
    }

    /**
     * {@inheritdoc}
     */
    public function removeEventSubscriber(EventSubscriber $subscriber): void
    {
        $this->removedSubscribers[] = $subscriber;

        parent::removeEventSubscriber($subscriber);
    }
}
