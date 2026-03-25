#!/bin/bash
set -e

REPO_DIR="$HOME/github-backfill"
BRANCH="main"
FILE_NAME="activity.txt"

mkdir -p "$REPO_DIR"
cd "$REPO_DIR"

if [ ! -d ".git" ]; then
  git init
  git checkout -b "$BRANCH"
fi

touch "$FILE_NAME"

for i in {6..0}; do
  DAY=$(date -v-"$i"d +"%Y-%m-%d")
  COUNT=$((1 + RANDOM % 12))

  echo "Day $DAY -> $COUNT commits"

  for ((j=1; j<=COUNT; j++)); do
    HOUR=$((9 + RANDOM % 11))     # 09..19
    MIN=$((RANDOM % 60))
    SEC=$((RANDOM % 60))

    COMMIT_TIME=$(printf "%s %02d:%02d:%02d" "$DAY" "$HOUR" "$MIN" "$SEC")

    echo "$COMMIT_TIME commit $j" >> "$FILE_NAME"
    git add "$FILE_NAME"

    GIT_AUTHOR_DATE="$COMMIT_TIME" \
    GIT_COMMITTER_DATE="$COMMIT_TIME" \
    git commit -m "chore: update $DAY #$j" >/dev/null
  done
done

echo "Готово. Дальше: git push -u origin $BRANCH"
