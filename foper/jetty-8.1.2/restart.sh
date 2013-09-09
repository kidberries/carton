#!/bin/bash
ABSOLUTE_FILENAME=`readlink -e "$0"`
DIRECTORY=`dirname "$ABSOLUTE_FILENAME"`
cd $DIRECTORY

./stop.sh
sleep 7 
./start.sh
