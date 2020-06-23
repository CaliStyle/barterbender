PROCESS=`ps -eaf|grep ynserver.php|grep -v grep|wc -l`
if [ $PROCESS -ne 0 ]; then
	PIDs=`ps -eaf|grep ynserver.php|awk {'print $2'}`
	for i in $PIDs;
	do
		echo " $i";
	done
else
	echo "there are no any process to stop"	
fi	
