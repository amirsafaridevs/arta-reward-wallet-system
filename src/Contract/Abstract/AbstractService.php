<?php

namespace ArtaRewardWalletSystem\Contract\Abstract;

use ArtaRewardWalletSystem\Contract\Interface\ServiceInterface;
use ArtaRewardWalletSystem\Contract\Interface\ContainerInterface;
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