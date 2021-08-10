# Laravel/Lumen Helpers

[![Total Downloads](https://poser.pugx.org/kodmanyagha/laravel-helpers/d/total.svg)](https://packagist.org/packages/kodmanyagha/laravel-helpers)
[![Latest Stable Version](https://poser.pugx.org/kodmanyagha/laravel-helpers/v/stable.svg)](https://packagist.org/packages/kodmanyagha/laravel-helpers)
[![Latest Unstable Version](https://poser.pugx.org/kodmanyagha/laravel-helpers/v/unstable.svg)](https://packagist.org/packages/kodmanyagha/laravel-helpers)
[![License](https://poser.pugx.org/kodmanyagha/laravel-helpers/license.svg)](https://packagist.org/packages/kodmanyagha/laravel-helpers)

Some beautiful functions here. String to Object conversion, make array or object everything, log current file and line number,
random date between given dates etc...

## Installation

This is classical composer package. You can install with one command line and start to use it:

```sh
composer require kodmanyagha/laravel-helpers
```

## Configuration

There isn't any configuration. Just install it.

## Available Functions

```
ma($anything)        Make array.
mo($anything)        Make object.

s2o($string)         String to Object.
o2s($object)         Object to Json string.

lgd(...$args)        Log::debug()
lgi(...$args)        Log::info()
lgw(...$args)        Log::warn()
lge(...$args)        Log::error()

password()           Generate password with APP_KEY salt.
pe()                 print_r and exit.
println($str)        Print string with PHP_EOL.
mysqlNow()           Current date and time with Mysql format.
randomDateTime($start, $end)
randomDate($start, $end)


```


