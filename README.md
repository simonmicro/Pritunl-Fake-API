# What is this? #
This neat script provides a little fake API to unlock all premium/enterprise/enterprise+ (here called ultimate) features of your own Pritunl VPN server. If Pritunl wouldn't be mostly free already, you could call this a crack. An Open Source crack.

## How to setup (api) (optional) ##
This is _optional_. You can simply use the default instance of this API (host is noted inside the `setup.sh` script) and profit from "automatic" updates.
Just transfer the files inside a public accessible folder on your webserver. Also make sure your instance has a valid SSL-certificate (Let's encrypt is enough).

## How to setup (server) ##
Take a look into the server folder. You _can_ use the pritunl source there (or just download this specific version from their GitHub repo) to compile a guaranteed compartible version for this API (you'll still need the `setup.sh` script) or just download any other version of the Pritunl server and try your luck.
After that log in into the dashboard (there should be a successmsg) and try to enter a serial. Just follow the hint if you enter an invalid command. A valid command would be `bad premium` or `active ultimate`.
Make sure to support the devs by buying the subscription for your enterprise or company!

### Nett2Know ###
* This way will also block any communication to the Pritunl servers - so no calling home :)
* The `ultimate` mode is still a little bit buggy. This is caused by some hacky workarounds to get all features displayed (the server is already unlocked). Caused by this workaround some items are maybe shown instead o being hidden. If you find such thing - just inform me about it.

Have fun with your new premium/enterprise/ultimate Pritunl instance!