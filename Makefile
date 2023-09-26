build:
	docker build -t rinha-de-compiler-php .

run:
	docker run -v ./source.rinha.json:/var/rinha/source.rinha.json --memory=2gb --cpus=2 dhr-rinha-de-compiler-php

run-dev:
	php index.php
