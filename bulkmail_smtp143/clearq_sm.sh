#!/bin/bash
while true
do
 php /var/www/html/bulkmail_smtp143/qdel.php >> qdel_err
 sleep 600
done
