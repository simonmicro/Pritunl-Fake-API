FROM goofball222/pritunl:latest

ARG API_SERVER_DOMAIN
ENV API_SERVER_DOMAIN $API_SERVER_DOMAIN

# Yes, you will need to copy it over into the build context...
COPY setup.py .

RUN chmod +x setup.py; python3 -u setup.py --install --api-server ${API_SERVER_DOMAIN:-}; rm setup.py