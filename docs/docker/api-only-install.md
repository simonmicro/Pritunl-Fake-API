# API Only: Docker

## Only installs the API (webserver) and not the Pritunl VPN itself.
This approach runs this API, either on port 80 or behind Traefik, either on docker swarm or single daemon.

You need to have docker up and running on your server.

- In your server, clone this repo, then `cd` to the cloned folder.
- Copy the docker-compose file provided in `<repo_root>/docker/api-only/docker-compose.yml` to
  the root of the cloned folder. 
  
  You shall now have: `<repo_root>/docker-compose.yml`
- Modify the `<repo_root>/docker-compose.yml` to fit your needs and config

  _Watch for volumes, docker swarm or single daemon, behind Traefik or not and the HOST value if behind traefik:_

   In case you run behind Traefik, you need to setup the traefik router HOST

   You need correctly setup traefik and docker network (here called proxy_external)

   **(!) Make sure the mount volumes match correctly.**

   * The first volume is the path to the www folder from the root of this repo.

   The path shall be a full path, or be next to this docker-compose.yml file. 

   No parent folder navigation like `../../../` is allowed by docker.

   * The second volume is the path to the nginx server config file.
   
   This needs the commited nginx server config (or your own adapted version) to work properly.
   
   See the file `<repo_root>/docker/api-only/conf.d/pritunl-fake-api.conf` for more details.
- Run the updated `docker-compose.yml` file in daemon mode with:

  `docker-compose up -d`