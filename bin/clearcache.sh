#!/bin/bash

sudo su www-data -c "/usr/local/zend/bin/php app/console cache:clear --env=prod --without-debug"

