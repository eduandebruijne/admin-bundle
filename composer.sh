#!/usr/bin/env sh

docker run --rm --interactive --tty \
  --volume $PWD:/app \
  composer "$@"
