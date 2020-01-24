# What is this? #
This neat script provides a little fake API to unlock all premium/enterprise/enterprise+ (here called ultimate) features of your own pritunl VPN server. If pritunl wouldn't be free you could call this a crack. An Open Source crack. Have fun!

## How to setup (api) (optional) ##
Just transfer the files inside the www folder to your webserver.
Make sure your fake API has a valid SSL-cert (Let's encrypt is helpful).

## How to setup (server) ##
Take a look into the server folder. You _can_ use the pritunl src there to compile a guaranteed compartible version for this fake API (you'll still need the `setup.sh` script) or just download any version of pritunl server and try your luck.
After that log in into the dashboard (there should be a successmsg) and try to enter a serial. Just follow the hint if you enter an invalid command. A valid command would be `active enterprise`.
Make sure to support the devs by buying the subscription for your enterprise or company!

### Nett2Know ###
This will also block any communication to pritunl - so no calling home :)
