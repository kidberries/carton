#!/bin/bash
#!/bin/bash
ABSOLUTE_FILENAME=`readlink -e "$0"`
DIRECTORY=`dirname "$ABSOLUTE_FILENAME"`
cd $DIRECTORY
java -DSTOP.PORT=9090 -DSTOP.KEY=i5Rules -jar start.jar --stop