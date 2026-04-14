#!/usr/bin/env bash
set -eu

sudo systemctl stop yxt-demo || true

if [ -f /srv/dadatun-demo/nginx/nginx.pid ]; then
	sudo kill "$(cat /srv/dadatun-demo/nginx/nginx.pid)" || true
fi

ss -ltnp | grep -E ':8081|:8443' || true
