ORIG_API_SERVER='app.pritunl.com'
ORIG_AUTH_SERVER='auth.pritunl.com'

if hash dialog 2>/dev/null; then
    echo "Dialog found..."
else
    echo "Error: Package 'dialog' missing!"
    exit 1
fi
if hash find 2>/dev/null; then
    echo "Find found..."
else
    echo "Error: Package 'find' missing!"
    exit 1
fi
if hash sed 2>/dev/null; then
    echo "Sed found..."
else
    echo "Error: Package 'sed' missing!"
    exit 1
fi

winX=80
winY=8
choices=$(dialog --menu "What can I do for you?" 0 $winX 0 "Change" "Changes the API endpoint to your choice" "Reset" "Changes the API endpoint back to $ORIG_API_SERVER" 2>&1 >/dev/tty)
ORIG_API_SERVER_ESCAPED=$(echo "$ORIG_API_SERVER" | sed -e 's/\./\\./g')
ORIG_AUTH_SERVER_ESCAPED=$(echo "$ORIG_AUTH_SERVER" | sed -e 's/\./\\./g')

get_fake_api() {
    FAKE_API_SERVER=$(dialog --title "Fake API address" --inputbox "Please enter the address from your faked API (with a valid HTTPS certificate). If you don't have one yourself, just leave the default." $winY $winX 'pritunl-api.simonmicro.de' 2>&1 >/dev/tty)
    FAKE_API_SERVER_ESCAPED=$(echo "$FAKE_API_SERVER" | sed -e 's/\./\\./g')
    FAKE_AUTH_SERVER="$FAKE_API_SERVER\/auth\/"
    FAKE_AUTH_SERVER_ESCAPED=$(echo "$FAKE_AUTH_SERVER" | sed -e 's/\./\\./g')
    echo "Please wait, while this script is modifying all necessary parts of the server. This can take up to several minutes..."
}

show_info() {
    dialog --msgbox "$1" $winY $winX
}

set -e

for choice in $choices
do
    case $choice in
        Change)
            get_fake_api
            find /usr/lib/pritunl/lib/python3.8 -type f -print0 | xargs -0 sed -i "s/$ORIG_API_SERVER_ESCAPED/$FAKE_API_SERVER_ESCAPED/g"
            find /usr/share/pritunl/www/ -type f -print0 | xargs -0 sed -i "s/$ORIG_API_SERVER_ESCAPED/$FAKE_API_SERVER_ESCAPED/g"
            find /usr/lib/pritunl/lib/python3.8 -type f -print0 | xargs -0 sed -i "s/$ORIG_AUTH_SERVER_ESCAPED/$FAKE_AUTH_SERVER_ESCAPED/g"
            find /usr/share/pritunl/www/ -type f -print0 | xargs -0 sed -i "s/$ORIG_AUTH_SERVER_ESCAPED/$FAKE_AUTH_SERVER_ESCAPED/g"
            sleep 4
            show_info "Changed $ORIG_API_SERVER to $FAKE_API_SERVER (and blocked any SSO server). Please make sure to restart the pritunl daemon now."
            ;;
        Reset)
            echo "Make sure to REMOVE ANY FAKED SUBSCRIPTION KEY (not by entering an other command - just remove them). You have now 30 seconds time to hit CTRL+C and do this."
            sleep 30
            get_fake_api
            find /usr/lib/pritunl/lib/python3.8 -type f -print0 | xargs -0 sed -i "s/$FAKE_API_SERVER_ESCAPED/$ORIG_API_SERVER_ESCAPED/g"
            find /usr/share/pritunl/www/ -type f -print0 | xargs -0 sed -i "s/$FAKE_API_SERVER_ESCAPED/$ORIG_API_SERVER_ESCAPED/g"
            find /usr/lib/pritunl/lib/python3.8 -type f -print0 | xargs -0 sed -i "s/$FAKE_AUTH_SERVER_ESCAPED/$ORIG_AUTH_SERVER_ESCAPED/g"
            find /usr/share/pritunl/www/ -type f -print0 | xargs -0 sed -i "s/$FAKE_AUTH_SERVER_ESCAPED/$ORIG_AUTH_SERVER_ESCAPED/g"
            sleep 4
            show_info "Changed $FAKE_API_SERVER to $ORIG_API_SERVER (and unblocked SSO features). Please make sure to restart the pritunl daemon now."
            ;;
    esac
done

exit 0
