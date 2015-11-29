<?php

namespace Interop\Container\Definition\Test;

use Interop\Container\Definition\DefinitionProviderInterface;

class ArrayDefinitionProvider implements DefinitionProviderInterface
{
    private $arrayDefinitions;

    public function __construct(array $arrayDefinitions = [])
    {
        $this->arrayDefinitions = $arrayDefinitions;
    }

    public function getDefinitions()
    {
        return $this->arrayDefinitions;
    }
}
