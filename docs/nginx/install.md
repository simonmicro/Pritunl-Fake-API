# HowTo install the selfhost api variant on Nginx

## Easy way:
Use docker and docker-compose files provided in `docker/api-only` folder.

See documentation [Docker Install](docs/docker/api-only-install.md).

The docker compose file has a detailed help in its top too. Read and Roll :)


## Hard way:
First, you need to install Nginx.
```bash
sudo apt-get install nginx
```

After that, install all of the relevant PHP modules:

```bash
sudo apt-get -y install php7.4-fpm php7.4-mysql php7.4-curl php7.4-gd php7.4-intl php-pear php-imagick php7.4-imap php-memcache
```

Then install certbot for free SSL certs:
```bash
sudo apt-get install -y certbot python3-certbot-nginx
```

After this, create a basic site config for the fake api server. Do this by creating a file under /etc/nginx/sites-available/ and create a symbolic link to /etc/nginx/sites-enabled. 
You can refer to the provided Nginx server block available in:
`<repo_root>/docker/api-only/conf.d/pritunl-fake-api.conf`

Then generate an SSL certificate for the website with certbot.
```bash
sudo certbot --nginx -d [PUBLIC_ACCESSIBLE_API_DOMAIN]
```

Once this is done, you should check if you have all the required loaded PHP modules for this server. You can check this by running php -m, and the output should list your PHP modules.

the output should look be something like:
```bash
#...
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
 #...
```

Then clone this repository if you haven't done this already and cd into the root of the project:
```bash
git clone https://gitlab.simonmicro.de/simonmicro/pritunl-fake-api.git
cd ./pritunl-fake-api
```

After this is done, copy over the API server files to the server and set permissions.
```bash
sudo cp -R ./www/* /var/www/html/
sudo chown www-data:www-data  -R /var/www/html
sudo chmod -R 774 /var/www/html/
```

For your convenience, a hardened Nginx configuration is provided to help you secure and improve your server,
Read it carefully before use and make sure you understand what it does.

See: `<repo_root>/docs/nginx/hard_nginx.conf`


Then restart Nginx to make sure all of the configuration is loaded.
```bash
sudo systemctl restart nginx
```

Once this is done, you should get a response when you visit

 `https://[PUBLIC_ACCESSIBLE_API_DOMAIN]/notification`!
