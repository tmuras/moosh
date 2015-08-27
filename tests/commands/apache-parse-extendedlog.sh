#!/bin/bash
source functions.sh

# install_db
# install_data

# cd $MOODLEDIR

if $MOOSHCMD apache-parse-perflog "../../data/performance.log" | grep -w "INSERT IGNORE INTO perflog (time,timestamp,url,memory_peak,includecount,contextswithfilters,filterscreated,textsfiltered,stringsfiltered,langcountgetstring,db_reads,db_writes,ticks,user,sys,cuser,csys,serverload,cache_mondodb_sets,cache_mondodb_misses,cache_mondodb_hits,cache_static_sets,cache_static_misses,cache_static_hits,cache_staticpersist_sets,cache_staticpersist_misses,cache_staticpersist_hits,cache_file_sets,cache_file_misses,cache_file_hits) VALUES ('32879','2015-08-17 12:37:25','/m29/my/','9032632','562','0','0','0','0','231','42','0','3','2','1','0','0','1','0','0','0','0','0','0','0','0','0','0','0','6');
"; then
:
else
  exit 1
fi

if $MOOSHCMD apache-parse-perflog "../../data/performance.log" | wc -l | grep 1
 then
  exit 0
else
  exit 2
fi





