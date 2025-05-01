#!/bin/bash
TODAY := $(shell date +'%Y-%m-%d')
CURRENT_BRANCH := $(shell git rev-parse --abbrev-ref HEAD)

help: ## Show this help message
	@echo "usage: make [target]"
	@echo
	@echo "targets:"
	@egrep "^(.+)\:\ ##\ (.+)" ${MAKEFILE_LIST} | column -t -c 2 -s ":#"

gitpush: ## git push m=any message
	clear;
	git add .; git commit -m "$(m)"; git push;

gp: ## git push m=any message
	clear;
	git add .; git commit -m "$(m)"; git push;

tw: ## git push to the current branch
	clear;
	# cd public/tpl; pnpx @tailwindcss/cli -i ./input.css -o ./assets/css/tw-4.1.5.css --watch
	cd backend_web/public/tpl; npm run build:styles

run-app-win: ## start serving app in localhost:8181
	cd backend_web; php -c php.ini -S localhost:8181 -t public

run-app-wsl: ## start serving app in localhost:8181
	cd backend_web; php -S localhost:8181 -t public