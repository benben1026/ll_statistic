#!/bin/bash
now=$(date -d "yesterday" +"%Y-%m-%d")
echo -e "\n\n###### $now ######" >> cron.log
curl "https://dash.keep.edu.hk/index.php/engagement/cron_job?date=$now" >> cron.log 2>&1
