#!/bin/sh
if [ "$(id -u)" != "0" ]; then
    echo "This script must be run as root." 1>&2
    exit 1
fi

cd /var/www 
chown -R www-data.www-data *          # reset file and directory ownership
find . -type d -exec chmod 755 {} \;  # directories are 755
find . -type f -exec chmod 644 {} \;  # files are 644
chmod +x bin/*                        # all files in bin/ are executable
chown root bin/$(basename $0)         # this file is owned by root

cd - 1>/dev/null

