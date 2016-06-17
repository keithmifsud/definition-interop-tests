# definition-interop compiler's test suite

Modules (aka packages or bundles) are widespread in modern frameworks. Unfortunately each framework has its own convention and tools for writing them. The goal of *container-interop* and more specifically *definition-interop* is to help developers write modules that can work in any framework.

*definition-interop* contains interfaces helping developers describe container definitions (objects that can be cast to a container entry).

This package contains a set of test suites that can be used to ensure that a container/compiler is indeed compatible with definition-interop.

## Installation

```
composer require --dev container-interop/definition-interop-tests:dev-master
```

## How does it work?

This package contains a number of definition instances, and the associated PHPUnit tests that should match those instances.

Container / compilers compatible with *container-interop* should be able to pass any of those tests.

## Usage

This package contains a `AbstractDefinitionCompatibilityTest` class. This is an abstract PHPUnit test class.
In your package, you should extend this class and implement the `compileDefinitions` method. This method should return a container-interop compatible container.

