<?php

namespace Creonit\MediaBundle;

use Creonit\MediaBundle\DependencyInjection\Compiler\VideoHandlerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CreonitMediaBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new VideoHandlerPass());
    }
}
