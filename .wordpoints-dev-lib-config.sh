#!/usr/bin/env bash

export DO_WP_CEPT=$(if [[ $TRAVIS_PHP_VERSION == '5.4' ]]; then echo 1; else echo 0; fi)
export WP_CEPT_SERVER='127.0.0.1:8888'

# Codeception requires PHP 5.4+.
if [[ $TRAVIS_PHP_VERSION == '5.2' || $TRAVIS_PHP_VERSION == '5.3' ]]; then
	CODESNIFF_PATH+=('!' -path "./tests/codeception/*")
fi

wpcept-setup() {

	if [[ $DO_WP_CEPT == 0 ]]; then
		return
	fi

	if [[ $WP_VERSION != '4.4' ]]; then
		export DISPLAY=:99.0
 		sh -e /etc/init.d/xvfb start
 	fi

	composer require --prefer-source codeception/codeception:2.1.4
	composer require --prefer-source lucatume/wp-browser:master@dev

	# We start the server up early so that it has time to prepare.
	php -S "$WP_CEPT_SERVER" -t "$WP_CORE_DIR" &
}

wpcept-run() {

	if [[ $DO_WP_CEPT == 0 ]]; then
		echo Not running codecept tests.
		return
	fi

	sed -i "s/http:\/\/wptests.local/$WP_CEPT_SERVER/" codeception.yml

	phantomjs --webdriver=4444 &

	# Give PhantomJS time to start.
	sleep 3

	vendor/bin/wpcept run --debug

	 cat "$WORDPOINTS_DEVELOP_DIR/tests/codeception/_output/savePointsReactionCept.fail.html"
}

fixed-codesniff-php-syntax() {

	if wpdl-codesniff-php-syntax | grep "^[Parse error|Fatal error]"; then
		return 1;
	fi;
}

alias codesniff-php-syntax='fixed-codesniff-php-syntax'

# EOF
