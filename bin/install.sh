#!/usr/bin/env bash
set -e

PROJECT_DIR="$( cd "$(dirname "$0")/.." && pwd )"

# Fail early if composer.lock and composer.json don't match
composer validate -d "$PROJECT_DIR"
composer install -d "$PROJECT_DIR"
