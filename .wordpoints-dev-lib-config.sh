#!/usr/bin/env bash

export DO_WP_CEPT=$(if [[ $TRAVIS_PHP_VERSION == '5.4' ]]; then echo 1; else echo 0; fi)

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
	php -S 127.0.0.1:8000 -t "$WP_CORE_DIR" >/dev/null 2>&1 &
}

wpcept-run() {

	if [[ $DO_WP_CEPT == 0 ]]; then
		echo Not running codecept tests.
		return
	fi

	sed -i "s/http:\/\/wptests.local/127.0.0.1:8000/" codeception.yml

	phantomjs --webdriver=4444 &

	# Give PhantomJS time to start.
	sleep 3

	vendor/bin/wpcept run
}

fixed-codesniff-php-syntax() {

	if wpdl-codesniff-php-syntax | grep "^[Parse error|Fatal error]"; then
		return 1;
	fi;
}

alias codesniff-php-syntax='fixed-codesniff-php-syntax'

# EOF
