<?php

namespace Interop\Container\Definition\Test;

use Assembly\ParameterDefinition;
use Assembly\Reference;
use Interop\Container\ContainerInterface;
use Interop\Container\Definition\DefinitionProviderInterface;
use Interop\Container\Definition\Test\Fixtures\UnsupportedDefinition;

abstract class AbstractDefinitionCompatibilityTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Takes a definition provider in parameter and returns a container containing the entries.
     *
     * @param DefinitionProviderInterface $definitionProvider
     * @return ContainerInterface
     */
    abstract protected function getContainer(DefinitionProviderInterface $definitionProvider);

    public function testInstance()
    {
        $referenceDefinition = new \Assembly\ObjectDefinition('\\stdClass');

        $assemblyDefinition = new \Assembly\ObjectDefinition('Interop\Container\Definition\Test\Fixtures\\Test');
        $assemblyDefinition->addConstructorArgument(42);
        $assemblyDefinition->addConstructorArgument(['hello' => 'world', 'foo' => new Reference('foo'), 'fooDirect' => $referenceDefinition]);

        $container = $this->getContainer(new ArrayDefinitionProvider([
            'bar' => $assemblyDefinition,
            'foo' => $referenceDefinition,
        ]));
        $result = $container->get('bar');

        $this->assertInstanceOf('Interop\Container\Definition\Test\Fixtures\\Test', $result);
        $this->assertEquals(42, $result->cArg1);
        $this->assertEquals('world', $result->cArg2['hello']);
        $this->assertInstanceOf('stdClass', $result->cArg2['foo']);
        $this->assertInstanceOf('stdClass', $result->cArg2['fooDirect']);
    }

    /**
     * Invalid objects (objects not extending one of the xxxDefinitionInterface interfaces) should trigger
     * an exception.
     *
     * @expectedException \Exception
     */
    public function testParameterException()
    {
        $assemblyDefinition = new \Assembly\ObjectDefinition('Interop\Container\Definition\Test\Fixtures\\Test');
        $assemblyDefinition->addConstructorArgument(new \stdClass());

        $this->getContainer(new ArrayDefinitionProvider([
            'bar' => $assemblyDefinition
        ]));
    }

    /**
     * Test method calls and property assignments
     */
    public function testInstancePropertiesAndMethodCalls()
    {
        $assemblyDefinition = new \Assembly\ObjectDefinition('Interop\Container\Definition\Test\Fixtures\\Test');
        $assemblyDefinition->addMethodCall('setArg1', 42);
        $assemblyDefinition->addPropertyAssignment('cArg2', 43);

        $container = $this->getContainer(new ArrayDefinitionProvider([
            'bar' => $assemblyDefinition,
        ]));
        $result = $container->get('bar');

        $this->assertInstanceOf('Interop\Container\Definition\Test\Fixtures\\Test', $result);
        $this->assertEquals(42, $result->cArg1);
        $this->assertEquals(43, $result->cArg2);
    }

    public function testParameter()
    {
        $assemblyDefinition = new \Assembly\ParameterDefinition('42');

        $container = $this->getContainer(new ArrayDefinitionProvider([
            'foo' => $assemblyDefinition,
        ]));
        $result = $container->get('foo');

        $this->assertEquals(42, $result);
    }

    public function testAlias()
    {
        $aliasDefinition = new \Assembly\Reference('bar');

        $assemblyDefinition = new \Assembly\ObjectDefinition('Interop\Container\Definition\Test\Fixtures\\Test');

        $container = $this->getContainer(new ArrayDefinitionProvider([
            'bar' => $assemblyDefinition,
            'foo' => $aliasDefinition,
        ]));
        $result = $container->get('foo');
        $result2 = $container->get('bar');

        $this->assertTrue($result === $result2);
    }

    public function testFactory()
    {
        $factoryAssemblyDefinition = new \Assembly\ObjectDefinition('Interop\Container\Definition\Test\Fixtures\\TestFactory');
        $factoryAssemblyDefinition->addConstructorArgument(42);

        $assemblyDefinition = new \Assembly\FactoryCallDefinition(new Reference('factory'), 'getTest');

        $container = $this->getContainer(new ArrayDefinitionProvider([
            'factory' => $factoryAssemblyDefinition,
            'test' => $assemblyDefinition,
        ]));
        $result = $container->get('test');

        $this->assertInstanceOf('Interop\Container\Definition\Test\Fixtures\\Test', $result);
        $this->assertEquals(42, $result->cArg1);
    }

    /**
     * @expectedException \Exception
     */
    public function testUnsupportedDefinition()
    {
        $definition = new UnsupportedDefinition();

        $container = $this->getContainer(new ArrayDefinitionProvider([
            'foo' => $definition,
        ]));
        $container->get('foo');
    }

    public function testRecursiveArrayObjectArguments()
    {
        $referenceDefinition = new \Assembly\ObjectDefinition('\\stdClass');

        $assemblyDefinition = new \Assembly\ObjectDefinition('Interop\Container\Definition\Test\Fixtures\\Test');
        $assemblyDefinition->addConstructorArgument(42);
        $assemblyDefinition->addConstructorArgument([
            'recursive' => [
                'hello' => 'world',
                'foo' => new Reference('foo'),
            ]
        ]);

        $container = $this->getContainer(new ArrayDefinitionProvider([
            'bar' => $assemblyDefinition,
            'foo' => $referenceDefinition,
        ]));
        $result = $container->get('bar');

        $this->assertInstanceOf('Interop\Container\Definition\Test\Fixtures\\Test', $result);
        $this->assertEquals(42, $result->cArg1);
        $this->assertEquals('world', $result->cArg2['recursive']['hello']);
        $this->assertInstanceOf('stdClass', $result->cArg2['recursive']['foo']);
    }

    /**
     * Check that sub-definitions are resolved in parameters, recursively.
     */
    public function testParametersContainingSubDefinitions()
    {
        $container = $this->getContainer(new ArrayDefinitionProvider([
            'parameter' => new ParameterDefinition([
                'abc' => new Reference('foo'),
            ]),
            'recursive' => new ParameterDefinition([
                'abc' => [
                    'def' => new Reference('foo'),
                ]
            ]),
        ]));

        $result = $container->get('parameter');
        $this->assertEquals('bar', $result['abc']);
        $result = $container->get('recursive');
        $this->assertEquals('bar', $result['abc']['def']);
    }
}
