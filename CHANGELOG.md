# 1.x branch
## 1.0 branch
### 1.0.3-beta4
* improved `AbstractEntity::getDocBlockInstance()`, `AbstractEntity::getTags()`
    and `AbstractMethodEntity::getParameters()` methods. The return values are
    now cached;
* added `functions.rendering` and `class.rendering` events, dispatched by
    `PhpDocMaker`. This allows easier identification of errors.

### 1.0.2-beta3
* now all tags for methods, properties and constants are shown correctly;
* added `TagEntity`, which now represents the tags;
* tags are now a collection (so `AbstractEntity::getTagsByName()` returns now a
    `Collection` instance). Added `AbstractEntity::hasTag()` method,
    `AbstractEntity::getTags()` and `AbstractEntity::getTagsGroupedByName()` methods;
* all entities implements the `ArrayAccess`, so now any "get" method can be used
    as the entity is an array;
* when you explicitly ask not to use the cache, the cache is emptied;
* added `createFromName()`, `getInterfaces()`, `getType()` and `getTraits()`
    methods to the `ClassEntity`
* `AbstractMethodEntity::getParameters()`, `ClassEntity::getConstants()`,
    `ClassEntity::getMethods()`, `ClassEntity::getProperties()`,
    `ClassesExplorer::getAllClasses()` and `ClassesExplorer::getAllFunctions()`
    methods return now a collection of entities;
* `ParentAbstractEntity::toSignature()` method renamed as `getSignature()`;
* added `TestCase::assertStringEqualsTemplate()` assertion method;
* removed `AbstractMethodEntity::getReturnDescription()`,
    `AbstractMethodEntity::getReturnTypeAsString()`,
    `AbstractMethodEntity::getThrowsTags()`,
    `DeprecatedTrait::getDeprecatedDescription()`, `DeprecatedTrait::isDeprecated()`,
    and `SeeTagsTrait::getSeeTags()` methods. Use instead the `TagEntity`
    and its methods;
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
