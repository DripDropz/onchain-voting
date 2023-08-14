#!/bin/bash

set -xe

## Functions
set_and_true() {
  if [[ -n "$1" ]] && $1; then return 0; else return 1; fi
}

until $(nc -zv $POSTGRESQL_WEB_DB_HOST 5432 &>/dev/null); do
  sleep 2s
done

php artisan package:discover

php artisan migrate --force

mkdir -p /var/run/supervisor
mkdir -p /ipc

if [[ "$APP_ENV" != "local" ]] && [[ "$APP_ENV" != "testing" ]]; then
  php artisan cache:clear
  php artisan view:clear    
  php artisan storage:link
  php artisan optimize
  php artisan event:cache
fi

case "$CONTAINER_ROLE" in
  queue | q)
    /usr/bin/supervisord -c /etc/supervisor/supervisord.queue.conf
    ;;
  scheduler | s)
    printenv | sed -e '/BASH_FUNC_module%%=()/,+500d' | sed 's/^\(.*\)$/export \1/g' > /root/.cron_env
    chmod 770 /opt/scripts/cron.sh
    crontab /etc/crontab
    /usr/bin/supervisord -c /etc/supervisor/supervisord.scheduler.conf
    ;;
  app | a)
    /usr/bin/supervisord -c /etc/supervisor/supervisord.conf
    ;;
  *)
    echo 'this container has no role... it is a sitting duck doing nothing!'
    echo 'Please $CONTAINER_ROLE to either queue, scheduler, or app'
    ;;
esac
