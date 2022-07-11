#!/usr/bin/env sh

docker run -it -v "$PWD":/usr/src/app -w /usr/src/app node:12 node_modules/.bin/webpack-cli
