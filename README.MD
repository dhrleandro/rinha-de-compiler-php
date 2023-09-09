# Rinha de Compiler - PHP Implementation

Interpretador implementado em PHP para a Rinha de Compiler

https://github.com/aripiprazole/rinha-de-compiler/tree/main

## SPECS

https://github.com/aripiprazole/rinha-de-compiler/blob/main/SPECS.md

## HOW TO RUN

**Clone**
```
git clone git@github.com:dhrleandro/rinha-de-compiler-php.git
```

### With Docker

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

### Without Docker

You need PHP 8 and Composer 2

```sh
composer install
php index.html
```
