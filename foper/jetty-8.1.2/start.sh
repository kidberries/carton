#!/bin/bash
ABSOLUTE_FILENAME=`readlink -e "$0"`
DIRECTORY=`dirname "$ABSOLUTE_FILENAME"`
cd $DIRECTORY

FILE_NAME_END=`date '+_%Y%m%d_%H%M%S_%s.out'`
./start_with_output.sh >'log/jetty_out'$FILE_NAME_END 2>'log/jetty_err'$FILE_NAME_END &
