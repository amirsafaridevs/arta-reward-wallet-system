<?php

namespace ArtaRewardWalletSystem\Contract\Abstracts;

use ArtaRewardWalletSystem\Contract\Interfaces\ServiceInterface;
use ArtaRewardWalletSystem\Contract\Interfaces\ContainerInterface;
use ArtaRewardWalletSystem\App\App;
abstract class AbstractService implements ServiceInterface
{
    /**
     * Container instance
     *
     * @var ContainerInterface
     */
    protected ContainerInterface $container;
    /**
     * Boot services after registration
     *
     * @param ContainerInterface $container
     * @return void
     */
    public function boot(): void
    {
       
    }

    /**
     * Get the application instance
     *
     * @return App
     */
    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    /**
     * Get the container instance
     *
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }
}   