<?php


namespace Interop\Container\Definition\Test\Fixtures;


use Assembly\ObjectDefinition;
use Interop\Container\Definition\DefinitionInterface;
use Interop\Container\Definition\DefinitionProviderInterface;

class TestDefinitionProvider implements DefinitionProviderInterface
{

    /**
     * Returns the definition to register in the container.
     *
     * @return DefinitionInterface[]
     */
    public function getDefinitions()
    {
        return [
            new ObjectDefinition('test', '\\stdClass')
        ];
    }
}