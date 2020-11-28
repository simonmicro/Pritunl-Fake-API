# HowTo install the selfhost api variant on Apache

First you want to install the apache2.
```bash
sudo apt-get install apache2
```
After install all of the relevent apache modules:
```bash
sudo apt-get -y install php7.4-mysql php7.4-curl php7.4-gd php7.4-intl php-pear php-imagick php7.4-imap php-memcache
```
Then install certbot for free ssl certs :
```bash
sudo apt-get install -y certbot
```
After this then create a basic site config for the fake api server, do this by creating a file under /etc/apache2/sites-enabled/000-default-le-ssl.conf with the example conf [example](docs/apache/000-default-le-ssl.conf).

Then generate a ssl certificate for the website with certbot.
```bash
sudo certbot -d [PUBLIC_ACCESSIBLE_API_DOMAIN]
```
Once this is done you should check if you have all of the required loaded php modules required for this server. You can check this by running `sudo apache2ctl -M` and the output should look be something like
```
sudo apache2ctl -M
Loaded Modules:
 core_module (static)
 so_module (static)
 watchdog_module (static)
 http_module (static)
 log_config_module (static)
 logio_module (static)
 version_module (static)
 unixd_module (static)
 access_compat_module (shared)
 alias_module (shared)
 auth_basic_module (shared)
 authn_core_module (shared)
 authn_file_module (shared)
 authz_core_module (shared)
 authz_host_module (shared)
 authz_user_module (shared)
 autoindex_module (shared)
 deflate_module (shared)
 dir_module (shared)
 env_module (shared)
 filter_module (shared)
 http2_module (shared)
 mime_module (shared)
 mpm_prefork_module (shared)
 negotiation_module (shared)
 php7_module (shared)
 proxy_module (shared)
 proxy_fcgi_module (shared)
 reqtimeout_module (shared)
 rewrite_module (shared)
 setenvif_module (shared)
 socache_shmcb_module (shared)
 ssl_module (shared)
 status_module (shared)
```

Then clone this repo if you've not done this already and `cd` into the root of the project:
```bash
git clone https://gitlab.simonmicro.de/simonmicro/pritunl-fake-api.git
cd ./pritunl-fake-api
```
After this is done copy over the API server files to the server and set permissions
```bash
sudo cp -R ./www/* /var/html/
sudo chown www-data:www-data  -R /var/www/html
sudo chmod -R 774 /var/www/html/
```
Then restart apache2 to make sure all of the configuration is loaded
```bash
sudo systemctl restart apache2
```
Once this is done you should get a response when you visit `https://[PUBLIC_ACCESSIBLE_API_DOMAIN]/notification`!
