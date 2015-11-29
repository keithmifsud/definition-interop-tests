<?php


namespace Interop\Container\Definition\Test\Fixtures;


use Interop\Container\Definition\DefinitionInterface;

class UnsupportedDefinition implements DefinitionInterface
{

    /**
     * Returns the value that identifies the entry in the container.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'foo';
    }
}
