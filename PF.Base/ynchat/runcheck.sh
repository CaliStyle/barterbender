#!/bin/sh
PS=`which ps`
PWD=`which pwd`
KILL=`which kill`
GREP=`which grep`
AWK=`which awk`
PHP=`which php`
phpPATH=$2"ynserver.php"

case "$1" in
  start)	
	if [ -z $phpPATH ]; then 
		echo "No script. Please type a script path"
	else
		PROCESS=`$PS -eaf|grep $phpPATH|grep -v grep|wc -l`				
		if [[ $PROCESS -eq 0 ]]; then
			$PHP -q $phpPATH > /dev/null &		
			echo "Started success!"			
		else
			echo "This process ready running"
		fi
	fi	
  ;;
  stop)	
	if [ -z $phpPATH ]; then 
		echo "No script. Please type a script path"
	else
		PROCESS=`$PS -eaf|grep $phpPATH|grep -v grep|wc -l`
		if [ $PROCESS -ne 0 ]; then
			PIDs=`$PS -eaf|$GREP $phpPATH|$GREP -v grep|$AWK {'print $2'}`
			for i in $PIDs;
			do
				$KILL -9 $i;
			done
			echo "Stoped!"
		else
			echo "Not run; to stop"	
		fi
	fi	 
  ;;
  status)
	if [ -z $phpPATH ]; then 
		echo "No chat file path"
	else
		PROCESS=`$PS -eaf|grep $phpPATH|grep -v grep|wc -l`
		if [ $PROCESS -eq 0 ]; then
			echo "Not running!"
		else
			echo "Running!"
		fi
	fi
  ;;
  restart|force-reload|reload)
        $0 stop
        $0 start
  ;;
  *)
        echo "Usage: start|stop|restart|force-reload|status"
        exit 1
esac

exit 0