#!/bin/sh

set -e

uid=$(stat -c %u /srv)
gid=$(stat -c %g /srv)

if [ $uid == 0 ] && [ $gid == 0 ]; then
    if [ $# -eq 0 ]; then
        php-fpm
    else
        exec "$@"
    fi
fi

sed -i -r "s/app-user:x:\d+:\d+:/app-user:x:$uid:$gid:/g" /etc/passwd
sed -i -r "s/app-users:x:\d+:/app-users:x:$gid:/g" /etc/group

chown $uid:$gid /home

if [ $# -eq 0 ]; then
    php-fpm
else
    exec gosu app-user "$@"
fi
