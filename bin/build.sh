#!/usr/bin/env bash

set -ex

rm -rf vendor
composer install --no-dev
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
rm composer.lock
rm gulpfile.js
rm .eslintrc
rm .travis.yml
rm package.json
rm package-lock.json
rm README.md
