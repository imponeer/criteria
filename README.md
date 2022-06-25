[![License](https://img.shields.io/github/license/imponeer/criteria.svg)](LICENSE)
[![GitHub release](https://img.shields.io/github/release/imponeer/criteria.svg)](https://github.com/imponeer/criteria/releases) [![PHP](https://img.shields.io/packagist/php-v/imponeer/criteria.svg)](http://php.net) 
[![Packagist](https://img.shields.io/packagist/dm/imponeer/criteria.svg)](https://packagist.org/packages/imponeer/criteria) [![Maintainability](https://api.codeclimate.com/v1/badges/6422b162cb9a4a1b84f4/maintainability)](https://codeclimate.com/github/imponeer/criteria/maintainability)

# Criteria

Small library to generate SQL *WHERE*, *ORDER BY*, *GROUP BY* query parts based ideas on [Xoops](//xoops.org) CMS [CriteriaElement](https://github.com/XOOPS/XoopsCore/blob/b6c8582ef294f85bde7e8e48f7475c1d36284c5e/xoops_lib/Xoops/Core/Kernel/CriteriaElement.php), [Criteria](https://github.com/XOOPS/XoopsCore/blob/b6c8582ef294f85bde7e8e48f7475c1d36284c5e/xoops_lib/Xoops/Core/Kernel/Criteria.php) and [CriteriaCompo](https://github.com/XOOPS/XoopsCore/blob/b6c8582ef294f85bde7e8e48f7475c1d36284c5e/xoops_lib/Xoops/Core/Kernel/CriteriaCompo.php) classes but rewritten everything to with [more open-source permissive license](LICENSE).

## Installation

To install and use this package, we recommend to use [Composer](https://getcomposer.org):

```bash
composer require imponeer/criteria
```

Otherwise, you need to include manually files from `src/` directory. 

## How to contribute?

If you want to add some functionality or fix bugs, you can fork, change and create pull request. If you not sure how this works, try [interactive GitHub tutorial](https://skills.github.com).

If you found any bug or have some questions, use [issues tab](https://github.com/imponeer/criteria/issues) and write there your questions.
