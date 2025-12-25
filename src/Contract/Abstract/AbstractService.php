<?php

namespace ArtaRewardWalletSystem\Contract\Abstract;

use ArtaRewardWalletSystem\Contract\Interface\ServiceInterface;
use ArtaRewardWalletSystem\App\App;
abstract class AbstractService implements ServiceInterface
{
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