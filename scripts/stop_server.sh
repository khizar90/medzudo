#!/bin/bash
isExistHttps = `pgrep apache2`
if [[ -n  $isExistHttps ]]; then
    service apache2 stop
fi
