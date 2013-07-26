#!/bin/bash

sudo php vendor/vrtxf/asset-symlink-generator/SymlinkPublicModule.php
chown www-data:www-data public/ -h -H -R