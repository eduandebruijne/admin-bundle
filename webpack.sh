#!/usr/bin/env sh

docker run --rm -it -v "$(pwd):/app" -w "/app" node:12-alpine yarn install
docker run -it -v "$PWD":/usr/src/app -w /usr/src/app node:12 node_modules/.bin/webpack-cli
