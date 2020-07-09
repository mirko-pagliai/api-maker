# 1.x branch
## 1.0 branch
### 1.0.1-beta2
* `PhpDocMakerCommand` now correctly handles PHP errors too (e.g. notice);
* `ClassesExplorer` throws a correct exception on missing Composer autoloader;
* added `AbstractEntity::getTagsByName()` method. Invalid tags will throw an exception;
* added `ClassEntity::getParentClass()` method;
* added `ConstantEntity::getValueAsString()` method, fixed bug for class costants
    with an array as value;
* fixed little bug for `ClassEntity::getConstants()` method;
* fixed little bug for binary file.

### 1.0.0-beta1
* first release.
