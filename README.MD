# Compiler Battle (Rinha de Compiler) - PHP Implementation

[en] | [[pt-br]](./doc/LEIA-ME.MD)

Interpreter implemented in PHP for the Compiler Battle

https://github.com/aripiprazole/rinha-de-compiler

## INTRODUCTION

This program will interpret a Rinha language AST in two ways:

1. Tree-walker Interpreter: interprets the AST directly in memory
2. Bytecode Interpreter: compiles the AST into Bytecode and interprets the bytecode directly in memory through a simple VM

The two methods will be executed in sequence and should give the same result.

For now the index.php file will only execute method 2.

## EXPERTIMENTS

The `./src/experiments` folder is not part of the project itself, but it contains fun studies that this competition gave me.

## HOW TO RUN

**Clone**
```
git clone git@github.com:dhrleandro/rinha-de-compiler-php.git
```

### With Docker

**Docker command**
```sh
docker build -t dhr-rinha-de-compiler-php .
docker run -v ./source.rinha.json:/var/rinha/source.rinha.json --memory=2gb --cpus=2 dhr-rinha-de-compiler-php
```

**Examples:**

Run in the project root folder.

```sh
docker run -v ./var/rinha/files/combination.json:/var/rinha/source.rinha.json --memory=2gb --cpus=2 dhr-rinha-de-compiler-php

docker run -v ./var/rinha/files/fib.json:/var/rinha/source.rinha.json --memory=2gb --cpus=2 dhr-rinha-de-compiler-php

docker run -v ./var/rinha/files/sum.json:/var/rinha/source.rinha.json --memory=2gb --cpus=2 dhr-rinha-de-compiler-php

docker run -v ./var/rinha/files/print.json:/var/rinha/source.rinha.json --memory=2gb --cpus=2 dhr-rinha-de-compiler-php

docker run -v ./var/rinha/files/test.json:/var/rinha/source.rinha.json --memory=2gb --cpus=2 dhr-rinha-de-compiler-php
```

or

**Makefile command**
```sh
make build
make run
```

### Without Docker

You need PHP 8 and Composer 2

```sh
composer install
php index.php
```

## SPECS

https://github.com/aripiprazole/rinha-de-compiler/blob/main/SPECS.md

## TODO

### VM Implementation

Feature                | Checked |
:----------------------|:-------:|
Int                    | OK
Str                    | OK
Call                   | OK
Binary                 | OK
Function               | OK
Let                    | OK
If                     | OK
Print                  | Partially
First                  | -
Second                 | -
Bool                   | -
Tuple                  | -
Var                    | OK
