#!/bin/bash
ABSOLUTE_FILENAME=`readlink -e "$0"`
DIRECTORY=`dirname "$ABSOLUTE_FILENAME"`
cd $DIRECTORY
#java -jar start.jar
rm `find ./log/ -atime +3` > /dev/null 2> /dev/null
java -DSTOP.PORT=9090 -Djetty.port=8080 -DSTOP.KEY=i5Rules -jar start.jar
