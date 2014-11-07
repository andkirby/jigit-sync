#!/bin/sh
echo "Fetching files from GitHub..."

OUTPUT=$(git clone https://github.com/chobie/jira-api-restclient.git 2>&1)
if echo "$OUTPUT" | grep -qE "error\:|fatal\:"; then
    echo "Ooops.. Something wrong."
    echo $OUTPUT
    exit 1
fi
echo $OUTPUT
OUTPUT=$(git clone https://github.com/andkirby/jigit-sync.git 2>&1)
if echo "$OUTPUT" | grep -qE "error\:|fatal\:"; then
    echo "Ooops.. Something wrong."
    echo $OUTPUT
    exit 1
fi
echo $OUTPUT

cd jigit-sync

echo "
Getting into jigit-sync directory..."

echo "Please set your JIRA credentials and JIRA URL (your password will be encrypted).""

while [ "$USERNAME" -eq "" ] ; do
    #read username
    printf "JIRA username: "
    read USERNAME
done
while [ "$PASSWORD" -eq "" ] ; do
    #read password
    printf "JIRA password: "
    read -s PASSWORD
done
while [ "$JIRA_URL" -eq "" ] ; do
    #read JIRA URL
    printf "JIRA URL (e.g. http://jira.example.com): "
    read JIRA_URL
done

echo $PASSWORD >> config/jira.password

echo "
app:
  jira:
    username: $USERNAME
    url: $JIRA_URL" >> config/local.yml

echo "Please take a look JiGIT Tool manual by commands:"
echo "
    cd jigit-sync
    php jira.php h"
