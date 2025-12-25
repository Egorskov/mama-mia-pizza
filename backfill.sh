FILE="activity.txt"

for i in {6..0}; do
  DAY=$(date -v-"$i"d +"%Y-%m-%d")

  COUNT=$((15 + RANDOM % 15))

  echo "creating $COUNT commits for $DAY"

  for ((j=1; j<=COUNT; j++)); do

    HOUR=$((9 + RANDOM % 9))
    MIN=$((RANDOM % 60))
    SEC=$((RANDOM % 60))

    DATE="$DAY $HOUR:$MIN:$SEC"

    echo "$DATE commit $j" >> $FILE

    git add $FILE

    GIT_AUTHOR_DATE="$DATE" \
    GIT_COMMITTER_DATE="$DATE" \
    git commit -m "activity $DAY $j"

  done
done
