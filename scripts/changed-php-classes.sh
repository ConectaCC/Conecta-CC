#!/bin/sh
# Lista, separadas por vírgula, as classes de backend/app criadas ou alteradas
# em relação à base (padrão: origin/main), incluindo o que ainda não foi commitado.
# Uso: scripts/changed-php-classes.sh [base]
set -eu

base=${1:-origin/main}
root=$(git rev-parse --show-toplevel)
fork=$(git -C "$root" merge-base "$base" HEAD)

git -C "$root" diff --name-only --diff-filter=AMR "$fork" -- 'backend/app/*.php' \
  | sed -e 's#^backend/app/#App/#' -e 's#\.php$##' -e 's#/#\\#g' \
  | paste -sd, -
