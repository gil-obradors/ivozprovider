<?php

namespace Ivoz\Kam\Domain\Model\UsersWatcher;

use Ivoz\Core\Domain\Model\EntityInterface;

interface UsersWatcherInterface extends EntityInterface
{
    /**
     * Get presentityUri
     *
     * @return string
     */
    public function getPresentityUri();

    /**
     * Get watcherUsername
     *
     * @return string
     */
    public function getWatcherUsername();

    /**
     * Get watcherDomain
     *
     * @return string
     */
    public function getWatcherDomain();

    /**
     * Get event
     *
     * @return string
     */
    public function getEvent();

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus();

    /**
     * Get reason
     *
     * @return string | null
     */
    public function getReason();

    /**
     * Get insertedTime
     *
     * @return integer
     */
    public function getInsertedTime();
}
