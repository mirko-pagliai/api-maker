# 1.x branch
## 1.0 branch
### 1.0.2-beta3
* added `ClassEntity::getType()` method;
* added `ClassEntity::createFromName()` static method;
* added `TestCase::assertStringEqualsTemplate()` assertion method;
* added template `class` element;
* updated `bootstrap` and `highlight` asset files.

### 1.0.1-beta2
* `PhpDocMakerCommand` now correctly handles PHP errors too (e.g. notice);
* each entity now has a coherent `__toString()` method, as well as a
    `toSignature()` method that returns a humanized version of its signature;
* the common mark is turned into html directly in the template, thanks to a Twig
    filter. Added the `PhpDocMaker::getTwig()` static method;
* added the `GetDeclaringClassTrait`, for methods, properties and constants;
* added the `GetTypeAsStringTrait`, for parameters and properties;
* `ClassesExplorer` throws a correct exception on missing Composer autoloader;
* added `AbstractEntity::getTagsByName()` method. Invalid tags will throw an exception;
* added `ClassEntity::getParentClass()` method;
* added `ParameterEntity::getDeclaringFunction()` method;
* added `ConstantEntity::getValueAsString()` method, fixed bug for class costants
    with an array as value;
* fixed little bug for `ClassEntity::getConstants()` method;
* fixed little bug for binary file.

### 1.0.0-beta1
* first release.
