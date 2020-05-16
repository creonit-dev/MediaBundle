<?php

namespace Creonit\MediaBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class VideoHandlerPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public function process(ContainerBuilder $container)
    {
        $handlersIds = $this->findAndSortTaggedServices('creonit_media.video_handler', $container);

        foreach ($handlersIds as $handlerId) {
            $container->getDefinition('Creonit\MediaBundle\VideoHandler\VideoResolver')->addMethodCall('addHandler', [new Reference((string)$handlerId)]);
        }
    }
}