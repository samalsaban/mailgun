#!/bin/bash
while true
do
 php /var/www/html/bulkmail_sendgrid143/qdel.php >> qdel_err
 sleep 3600
done
