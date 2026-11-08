#!/bin/sh
# Lista, separados por vírgula, os trechos de frontend/src criados ou alterados em relação
# à base (padrão: origin/main), no formato do Stryker (src/arquivo.tsx:10-14), incluindo o
# que ainda não foi commitado. Testes, código gerado (shadcn e schema da API) e o main.tsx ficam fora.
# Uso: scripts/changed-front-lines.sh [base]
set -eu

base=${1:-origin/main}
root=$(git rev-parse --show-toplevel)
fork=$(git -C "$root" merge-base "$base" HEAD)

git -C "$root" diff -U0 --diff-filter=AMR "$fork" -- 'frontend/src/*.ts' 'frontend/src/*.tsx' \
  | awk '
      /^\+\+\+ / { file = substr($2, 3); sub(/^frontend\//, "", file); next }
      /^@@ / {
        split($3, added, ",")
        start = substr(added[1], 2); count = (2 in added) ? added[2] : 1
        if (count > 0) print file ":" start "-" (start + count - 1)
      }' \
  | grep -vE '\.test\.tsx?:|^src/test/|^src/components/ui/|^src/lib/api/schema\.d\.ts:|^src/main\.tsx:|^src/vite-env\.d\.ts:' \
  | paste -sd, -
