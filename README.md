# grumphp-typo3scan-task

GrumPHP task to scan the TYPO3 extensions for deprecations and code changes based on https://github.com/Tuurlijk/typo3scan.

## grumphp.yml

```yaml
typo3scan:
    extension_paths:
        - 'extensions/myext_one'
        - 'extensions/myext_two'
    types_of_changes: ~
    indicators: ~
    target_version: ~
extensions:
    - Pluswerk\GrumPHPTypo3ScanTask\Loader\ExtensionLoader
```

**extension_paths**

_Default: []_

The paths to the extensions (folder), which should be scanned. You need to give at least one path to scan.

**types_of_changes**

_Default: ['breaking','deprecation','feature','important']_

The types of changes, which should be scanned for. Possible values: _breaking, deprecation, feature, important_

**indicators**

_Default: ['strong','weak']_

Which kind of violations should be searched for. Possible values: _strong, weak_

**target_version**

_Default: null_

The TYPO3 version to test. With default value the currently latest TYPO3 version is used. Possible values: _7,8,9,10_

For further information about the options see https://github.com/Tuurlijk/typo3scan#usage.