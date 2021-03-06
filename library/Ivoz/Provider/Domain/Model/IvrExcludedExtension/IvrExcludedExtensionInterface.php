<?php

namespace Ivoz\Provider\Domain\Model\IvrExcludedExtension;

use Ivoz\Core\Domain\Model\LoggableEntityInterface;

interface IvrExcludedExtensionInterface extends LoggableEntityInterface
{
    public function getChangeSet();

    /**
     * Set ivr
     *
     * @param \Ivoz\Provider\Domain\Model\Ivr\IvrInterface $ivr
     *
     * @return static
     */
    public function setIvr(\Ivoz\Provider\Domain\Model\Ivr\IvrInterface $ivr = null);

    /**
     * Get ivr
     *
     * @return \Ivoz\Provider\Domain\Model\Ivr\IvrInterface
     */
    public function getIvr();

    /**
     * Set extension
     *
     * @param \Ivoz\Provider\Domain\Model\Extension\ExtensionInterface $extension
     *
     * @return static
     */
    public function setExtension(\Ivoz\Provider\Domain\Model\Extension\ExtensionInterface $extension);

    /**
     * Get extension
     *
     * @return \Ivoz\Provider\Domain\Model\Extension\ExtensionInterface
     */
    public function getExtension();
}
