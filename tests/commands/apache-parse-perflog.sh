#!/bin/bash
source functions.sh

install_db
# install_data

cd $MOODLEDIR

if moosh apache-parse-perflog "$MOODLEDATA/apachelog.log" | grep -w "INSERT IGNORE INTO perflog (time,timestamp,url,memory_peak,includecount,contextswithfilters,filterscreated,textsfiltered,stringsfiltered,langcountgetstring,db_reads,db_writes,ticks,user,sys,cuser,csys,serverload) VALUES ('88743','2014-09-25 10:09:44','/workspace/moodle26/theme/image.php?theme=standard&component=core&rev=-1&image=t/switch_minus','4058800','69','0','0','0','0','7','5','0','10','3','2','0','0','235')"; then
  exit 0
else
  exit 1
fi
