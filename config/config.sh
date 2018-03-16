#!/bin/bash
#function
funchmod(){
	myPath=$1
	if [ -d "$myPath" ]; then
		#echo "$myPath dir  exist"
		sudo chmod -R 777 $1
		return 1
	else
		echo "$myPath dir no exist"
		return 0
	fi
}

cd $(cd `dirname $0`;cd ..; pwd)
#search
funchmod "www/assets"
funchmod "cpdb/runtime"
funchmod "api/runtime"
