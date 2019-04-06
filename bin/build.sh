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
npm start
curl -L https://raw.githubusercontent.com/fumikito/wp-readme/master/wp-readme.php | php
# Remove files
rm -rf node_modules
rm -rf tests
rm -rf bin
rm phpunit.xml.dist
rm .distignore
rm .gitignore
rm .svnignore
rm composer.json
rm composer.lock
rm gulpfile.js
rm .eslintrc
rm .travis.yml
rm package.json
rm package-lock.json
rm README.md
rm -rf build
