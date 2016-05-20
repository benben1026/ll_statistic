now=$(date -d "yesterday" +"%Y-%m-%d")
wget http://127.0.0.1:4567/ll_statistic/index.php/engagement/cron_job?date=$now

