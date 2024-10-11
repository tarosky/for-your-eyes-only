#!/usr/bin/env bash

set -ex

if [ -d vendor ]; then
    rm -rf vendor
fi
composer install --no-dev
if [ -d node_modules ]; then
    rm -rf node_modules
fi
npm install
npm run build
