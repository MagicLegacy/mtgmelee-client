#!/usr/bin/env bash

PROJECT_DIR="$( cd "$(dirname "$0")/.." && pwd )"

cd ${PROJECT_DIR}

case "$1" in
    static)
        make phpcs
        ;;
    cs-fix)
        make phpcbf
        ;;
    unit)
        make tests
        ;;
    testdox)
        make testdox
        ;;
    php74-compatibility)
        make php74compatibility
        ;;
    php80-compatibility)
        make php80compatibility
        ;;
    *)
        echo "Use $0 {static|cs-fix|unit|testdox|php{74,80}-compatibility} in order to run static or unit tests"
        exit 1;
        ;;
esac
