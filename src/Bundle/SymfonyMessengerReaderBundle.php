<?php

declare(strict_types=1);

namespace Rocky\SymfonyMessengerReader\Bundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SymfonyMessengerReaderBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass($pass);
    }
}
