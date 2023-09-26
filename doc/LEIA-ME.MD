# Rinha de Compiler - PHP Implementation

[[en]](../README.MD) | [pt-br]

Interpretador implementado em PHP para a Rinha de Compiler

https://github.com/aripiprazole/rinha-de-compiler

## INTRODUÇÃO

Este programa interpretará uma AST da linguagem Rinha de duas maneiras:

1. Tree-walker Interpreter: interpreta a AST diretamente na memória
2. Bytecode Interpreter: compila a AST em Bytecode e interpreta o bytecode diretamente na memória através de uma simples VM

Os dois métodos serão executados em sequência e deverão apresentar o mesmo resultado.

Por enquanto o arquivo index.php irá executar apenas o método 2.

## EXPERTIMENTS

A pasta `./src/experiments` não faz parte do projeto em si, mas contém estudos divertidos que esta competição me proporcionou.

## COMO EXECUTAR

**Clone**
```
git clone git@github.com:dhrleandro/rinha-de-compiler-php.git
```

### Com Docker

**Docker command**
```sh
docker build -t dhr-rinha-de-compiler-php .
docker run -v ./source.rinha.json:/var/rinha/source.rinha.json --memory=2gb --cpus=2 dhr-rinha-de-compiler-php
```

or

**Makefile command**
```sh
make build
make run
```

### Sem Docker

Você precisa do PHP 8 e Composer 2

```sh
composer install
php index.php
```

## SPECS

https://github.com/aripiprazole/rinha-de-compiler/blob/main/SPECS.md
