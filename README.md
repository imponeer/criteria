[![License](https://img.shields.io/github/license/imponeer/criteria.svg)](LICENSE)
[![GitHub release](https://img.shields.io/github/release/imponeer/criteria.svg)](https://github.com/imponeer/criteria/releases) [![PHP](https://img.shields.io/packagist/php-v/imponeer/criteria.svg)](http://php.net) 
[![Packagist](https://img.shields.io/packagist/dm/imponeer/criteria.svg)](https://packagist.org/packages/imponeer/criteria)

# Criteria

A small PHP library to generate SQL *WHERE*, *ORDER BY*, and *GROUP BY* query parts. Inspired by [Xoops](//xoops.org) CMS classes ([CriteriaElement](https://github.com/XOOPS/XoopsCore/blob/b6c8582ef294f85bde7e8e48f7475c1d36284c5e/xoops_lib/Xoops/Core/Kernel/CriteriaElement.php), [Criteria](https://github.com/XOOPS/XoopsCore/blob/b6c8582ef294f85bde7e8e48f7475c1d36284c5e/xoops_lib/Xoops/Core/Kernel/Criteria.php), and [CriteriaCompo](https://github.com/XOOPS/XoopsCore/blob/b6c8582ef294f85bde7e8e48f7475c1d36284c5e/xoops_lib/Xoops/Core/Kernel/CriteriaCompo.php)), but fully rewritten and released under a more permissive open-source [license](LICENSE).

## Installation

The recommended way to install this package is via [Composer](https://getcomposer.org):

```bash
composer require imponeer/criteria
```

Alternatively, you can manually include the files from the `src/` directory.

## Usage

Here is a basic example of how to use the Criteria library:

```php
use Imponeer\Criteria\CriteriaItem;
use Imponeer\Criteria\CriteriaCompo;

$criteria = new CriteriaCompo();
$criteria->add(new CriteriaItem('status', 'active'));
$criteria->add(new CriteriaItem('age', 18, '>='));

// Generate SQL WHERE clause
$where = $criteria->render();
// $where will be: "(`status` = 'active' AND `age` >= 18)"
```

## API Documentation

Full API documentation is available in the [repository wiki](https://github.com/imponeer/criteria/wiki). Documentation is automatically updated with every release.

## Development

For development, use the following Composer commands:

- Run tests:
  ```bash
  composer test
  ```
- Run static analysis:
  ```bash
  composer phpstan
  ```
- Run code style checks:
  ```bash
  composer phpcs
  ```

## How to contribute?

If you want to add functionality or fix bugs, fork the repository, make your changes, and create a pull request.

If you find any bugs or have questions, please use the [issues tab](https://github.com/imponeer/criteria/issues) to report them or ask questions.
