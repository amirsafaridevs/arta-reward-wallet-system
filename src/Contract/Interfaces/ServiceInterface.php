<?php

namespace ArtaRewardWalletSystem\Contract\Interfaces;

/**
 * Service Interface
 * 
 * Contract for service implementations
 */
interface ServiceInterface
{
    /**
     * Boot services after registration
     *
     * @param ContainerInterface $container
     * @return void
     */
    public function boot(): void;
}