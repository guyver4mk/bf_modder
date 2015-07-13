#!/bin/bash
SCRIPT=$(php /var/www/html/cron/apk_cron_job.php)
SCRIPT="APK Run -":$SCRIPT
echo  $SCRIPT >> /var/www/html/tmp/cronlog
