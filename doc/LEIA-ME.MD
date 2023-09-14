# Rinha de Compiler - PHP Implementation

[[en]](../README.MD) | [pt-br]

Interpretador implementado em PHP para a Rinha de Compiler

https://github.com/aripiprazole/rinha-de-compiler/tree/main

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
docker build -t rinha-de-compiler-php .
docker run -it --memory="2g" --memory-swap="0" --cpus="2" rinha-de-compiler-php
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
