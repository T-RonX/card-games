<?php

declare(strict_types=1);

namespace App\Security\Factory;

use App\Security\Authentication\Provider\AnonymousPlayerProvider;
use App\Security\Firewall\AnonymousPlayerListener;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AnonymousPlayerFactory implements SecurityFactoryInterface
{
    public function create(ContainerBuilder $container, string $id, array $config, string $user_provider, ?string $default_entry_point): array
    {
        $provider_id = 'security.authentication.provider.anonymous_player.' . $id;
        $container->setDefinition($provider_id, new ChildDefinition(AnonymousPlayerProvider::class));

        $listener_id = 'security.authentication.listener.anonymous_player.' . $id;
        $container->setDefinition($listener_id, new ChildDefinition(AnonymousPlayerListener::class))
            ->addMethodCall('setIdentificationPath', [$config['identification_path']])
            ->addMethodCall('setValidationPath', [$config['validation_path']])
            ->addMethodCall('setSuccessPath', [$config['success_path']])
            ->addMethodCall('setIdentificationFormType', [$config['identification_form_type']])
            ->addMethodCall('setIdentificationFormField', [$config['identification_form_field']])
            ->addMethodCall('setAllowedPaths', [$container->getParameter('allowed_anonymous_paths')]);

        return [$provider_id, $listener_id, $default_entry_point];
    }

    public function getPosition(): string
    {
        return 'pre_auth';
    }

    public function getKey(): string
    {
        return 'anonymous_player';
    }

    /**
     * @param ArrayNodeDefinition|NodeDefinition $builder
     */
    public function addConfiguration(NodeDefinition $builder): void
    {
        $builder
            ->children()
            ->scalarNode('identification_path')->isRequired()->end()
            ->scalarNode('validation_path')->isRequired()->end()
            ->scalarNode('success_path')->isRequired()->end()
            ->scalarNode('identification_form_type')->isRequired()->end()
            ->scalarNode('identification_form_field')->isRequired()->end()
            ->end();
    }
}