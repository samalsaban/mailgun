#!/bin/bash
php /var/www/html/bulkmail_smtp143/caller_stopautosmtp.php $1 
sleep 5
php /var/www/html/bulkmail_smtp143/caller_stopautosmtp.php $1 

