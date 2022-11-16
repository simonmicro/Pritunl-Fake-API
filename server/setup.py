#!/usr/lib/python3
import os
import glob
import time
import base64
import argparse

originalApiServer = 'app.pritunl.com'
originalAuthServer = 'auth.pritunl.com'
newApiServer = 'pritunl-api.simonmicro.de'
searchIn = [*glob.glob('/usr/lib/python3*'), '/usr/lib/pritunl/', '/usr/share/pritunl/www/', '/usr/lib/pritunl/', '/usr/share/pritunl/www/']

print("  ____       _ _               _   _____     _             _    ____ ___ ")
print(" |  _ \ _ __(_) |_ _   _ _ __ | | |  ___|_ _| | _____     / \  |  _ \_ _|")
print(" | |_) | '__| | __| | | | '_ \| | | |_ / _` | |/ / _ \   / _ \ | |_) | | ")
print(" |  __/| |  | | |_| |_| | | | | | |  _| (_| |   <  __/  / ___ \|  __/| | ")
print(" |_|   |_|  |_|\__|\__,_|_| |_|_| |_|  \__,_|_|\_\___| /_/   \_\_|  |___|")
print("                                                                         ")

sel = None
interactive = True
parser = argparse.ArgumentParser()
parser.add_argument('--install', type=str, default='DEFAULT', nargs='?', help='Do not ask and install new API endpoint.')
parser.add_argument('--reset', type=str, default='DEFAULT', nargs='?', help='Do not ask and remove new API endpoint.')
args = parser.parse_args()

if args.install != 'DEFAULT':
    interactive = False
    newApiServer = args.install if args.install is not None else newApiServer
    sel = 'I'
if args.reset != 'DEFAULT':
    interactive = False
    newApiServer = args.reset if args.reset is not None else newApiServer
    sel = 'R'

if interactive:
    while sel not in ['I', 'R', 'B', 'Q']:
        sel = input('[I]nstall, [R]eset, [B]uy Pritunl, [Q]uit? ').upper()
    print()

def doTheReplace(fromApiStr, toApiStr, fromAuthStr, toAuthStr):
    print(f'Okay. We will change "{fromApiStr}" to "{toApiStr}" and "{fromAuthStr}" to "{toAuthStr}" now...')
    numFiles = 0
    for i in range(len(searchIn)):
        print(f'[{i+1}/{len(searchIn)}] Replacing in {searchIn[i]}...')
        for p, d, f in os.walk(searchIn[i]):
            for ff in f:
                try:
                    fh = open(os.path.join(p, ff), 'r')
                    lines = fh.read()
                    fh.close()
                    newLines = lines.replace(fromApiStr, toApiStr)
                    newLines = newLines.replace(fromAuthStr, toAuthStr)
                    # Special case for changes from c1772d9b3268f91de409ad552e3d4d54d5ae1125
                    newLines = newLines.replace(base64.b64encode(f'https://{fromApiStr}/subscription'.encode()).decode(), base64.b64encode(f'https://{toApiStr}/subscription'.encode()).decode())
                    if newLines != lines:
                        numFiles += 1
                        fh = open(os.path.join(p, ff), 'w')
                        fh.writelines(newLines)
                        fh.close()
                except UnicodeDecodeError:
                    # Brrr - binary files...
                    pass
    print(f'Modified {numFiles} files in {len(searchIn)} paths.')

if sel == 'I':
    if interactive:
        print(f'By default, the Pritunl API endpoint is hosted at "{originalApiServer}".')
        print(f'In case you want to use your own instance, you also have to support HTTPS!')
        print(f'Note, that the SSO implementation of Pritunl is hosted at their servers (closed source) and will just be "disabled".')
        ownApiServer = input(f'Please enter the new API endpoint [{newApiServer}]: ')
        if ownApiServer == '':
            ownApiServer = newApiServer
    else:
        ownApiServer = newApiServer
    doTheReplace(originalApiServer, ownApiServer, originalAuthServer, ownApiServer + '/auth/')
    print('Please make sure to restart the Pritunl daemon now and please support the developer.')
elif sel == 'R':
    if interactive:
        print(f'To properly revert any changes to your Pritunl server, this script must exactly know what (custom) API endpoint you have choosen.')
        ownApiServer = input(f'Please enter the current API endpoint [{newApiServer}]: ')
        if ownApiServer == '':
            ownApiServer = newApiServer
        print('Make sure to REMOVE ANY FAKED SUBSCRIPTION KEY (by not entering an other command - just remove them). You have now 30 seconds time to hit CTRL+C and do this.')
        time.sleep(30)
    else:
        ownApiServer = newApiServer
    doTheReplace(ownApiServer, originalApiServer, ownApiServer + '/auth/', originalAuthServer)
    print('Please make sure to restart the Pritunl daemon now.')
elif sel == 'B':
    print('Sure thing, buddy... Why did you try to use this?')
    print('Visit https://pritunl.com/ for you own license!')
    try:
        import webbrowser
        webbrowser.open('https://pritunl.com/')
        print('Let me help you...')
    except:
        pass
elif sel == 'Q':
    print('Bye!')
