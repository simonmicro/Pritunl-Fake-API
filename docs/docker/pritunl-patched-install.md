# Fully Patched Pritunl: Using Docker

## Only installs the API (webserver) and not the Pritunl VPN itself.
You need to have docker up and running on your server.

This uses the docker image for Pritunl by `goofball222/pritunl` and installs the fake api hooks directly into it.


Step:

- In your server, clone this repo, then `cd` to the cloned folder.
- Go to the `docker` folder of the repo.
- Read the `<repo_root>/docker/docker-compose.yml` file carefully and edit to fit your needs (ports, volumes, network, server domain...)
- Run the `docker-compose.yml` file in daemon mode with:
  
  `docker-compose up -d`

    - This will `docker build` the patched pritunl container and run it on the following ports:
      - Under this port the Pritunl web interface will be exposed (for reverse proxies)
        
        *9700:9700*
    
      - The following are the two default ports for the tcp+udp servers (you may edit these as needed!)
        
        *1194:1194*
        
        *1194:1194/udp*

