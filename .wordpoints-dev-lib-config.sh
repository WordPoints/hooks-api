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
	composer require --prefer-source lucatume/wp-browser:1.10.11

	# We start the server up early so that it has time to prepare.
	php -S "$WP_CEPT_SERVER" -t "$WP_CORE_DIR" &
}

wpcept-run() {

	if [[ $DO_WP_CEPT == 0 ]]; then
		echo Not running codecept tests.
		return
	fi

	sed -i "s/http:\/\/wptests.local/$WP_CEPT_SERVER/" codeception.dist.yml

	phantomjs --webdriver=4444 --webdriver-loglevel=DEBUG &

	# Give PhantomJS time to start.
	sleep 3

	vendor/bin/wpcept run --debug

	ls "$PROJECT_DIR"/../tests/codeception/_output/
	ls /tmp/wordpress/src/
	cat "$PROJECT_DIR/../tests/codeception/_output/pointsreactionupdate.cept.fail.html"
}

fixed-setup-composer() {

	if [[ $DO_CODE_COVERAGE == 1 && $TRAVISCI_RUN == phpunit ]]; then
		composer require --prefer-source satooshi/php-coveralls:0.7.0
		mkdir -p build/logs
		return;
	fi
}

alias setup-composer='fixed-setup-composer'

# EOF
