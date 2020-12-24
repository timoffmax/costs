ssh_app:
	docker-compose exec costs-app /bin/bash

js_watch:
	docker-compose exec costs-app npm run watch

js_dev:
	docker-compose exec costs-app npm run watch

js_prod:
	docker-compose exec costs-app npm run watch
