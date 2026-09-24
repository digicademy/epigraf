#!/usr/bin/env bash
exec > logs/start-selenium.log 2>&1   # everything from here on goes to the log
set -x                                # also log every command being run

set -e
mkdir -p /tmp/sessions && chown -R www-data /tmp/sessions
bin/cake setup tmp
bin/cake cache clear_all
chown -R www-data /tmp/cache

pkill -9 -f geckodriver || true
pkill -9 -f firefox     || true
pkill -9 -f Xvfb        || true

for i in $(seq 1 30); do
  if ! (echo > /dev/tcp/127.0.0.1/4444) 2>/dev/null; then
    echo "Port 4444 free"; break
  fi
  echo "Waiting for port 4444..."; sleep 1
done

MOZ_HEADLESS=1 geckodriver --port=4444 --host=127.0.0.1 --log=warn \
  > logs/gecko.log 2>&1 &

for i in $(seq 1 30); do
  if (echo > /dev/tcp/127.0.0.1/4444) 2>/dev/null; then
    echo "Geckodriver up"; exit 0
  fi
  sleep 1
done

echo "Geckodriver failed to start"
cat logs/gecko.log
exit 1
