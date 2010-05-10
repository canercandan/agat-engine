#!/usr/bin/env sh

PATH_SCRIPT="../src/byFile.cws"
OPTIONS="-nologo -stack 8000 -path `pwd` -script ${PATH_SCRIPT}"

CW="codeworker"
MKDIR="mkdir -p"
RM="rm -f"

if [ "$1" = "" ]; then
    echo "No file given in argument"
    exit 42
fi

${CW} ${OPTIONS} -args $*
${RM} *.out
