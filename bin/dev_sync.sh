#!/bin/bash

set -e

root="$(git rev-parse --show-toplevel)"

# shellcheck disable=SC1090,SC1091
source "$root/.env"

rsync -a "$XZ_PRODUCTS_JSON_DIR" "$XZ_PRODUCTS_TRACKED_DIR"
rsync -a --delete "$XZ_PRODUCTS_TRACKED_DIR" "$XZ_PRODUCTS_MU_PLUGIN_DIR"
