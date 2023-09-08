build:
	docker build -t rinha-de-compiler-php .

run:
	docker run -it --memory="2g" --memory-swap="0" --cpus="2" rinha-de-compiler-php

run-dev:
	php index.php
