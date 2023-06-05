#!/usr/bin/env bash

# This script is used to adjust the PHP examples style guide for use in the PHP client library.

# Ref: sed to work on both Unix and MacOS: https://stackoverflow.com/a/44864004

# Replace <file>official/docs/php</file> with <file>lib</file> and <file>test</file>
sed -i.bak 's/<file>official\/docs\/php<\/file>/<file>lib<\/file>\n\t<file>test<\/file>/g' phpcs.xml && rm phpcs.xml.bak
