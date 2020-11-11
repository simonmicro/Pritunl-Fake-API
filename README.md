# What is this? #
This neat script provides a little fake API to unlock all premium/enterprise/enterprise+ (here called ultimate) features of your own Pritunl VPN server. If Pritunl wouldn't be mostly free already, you could call this a crack. An Open Source crack.

## How to setup (server) ##
Take a look into the `server` folder: You _could_ use the Pritunl source there (or just download this specific version from their GitHub repo) to compile a guaranteed compartible version for this API (you'll still need the `setup.sh` script) or just download any other version of the Pritunl server and try your luck.
After that log in into the dashboard - there should be a "Update Notification":

![login-msg](docs/login-msg.png)

Now try to enter any serial key for your subscription and just follow the hints/notes if you enter an invalid command:

![enter-something](docs/enter-something.png)

A valid command would be `bad premium` or `active ultimate`:

![active-ultimate](docs/active-ultimate.png)

When everything worked, your subscription should now look like this:

![done](docs/done.png)

Make sure to support the developers by buying the choosen subscription for your enterprise or company!

## How to setup (api) (optional) ##
This is _optional_. You can simply use the default instance of this API (host is noted inside the `setup.sh` script) and profit from "automatic" updates.
Just transfer the `www` files inside a public accessible folder on your webserver. Also make sure your instance has a valid SSL-certificate (Let's encrypt is enough), otherwise it may won't work

### Nett2Know ###
* This modification will also block any communication to the Pritunl servers - so no calling home :)
* The `ultimate` mode is still a little bit buggy. This is caused by some hacky workarounds to get all features displayed (the server is already unlocked). Caused by this workaround some items are maybe shown instead o being hidden. If you find such thing - just inform me about it.

Have fun with your new premium/enterprise/ultimate Pritunl instance!
