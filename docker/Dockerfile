FROM goofball222/pritunl:latest

# Yes, you will need to copy it over into the build context...
COPY setup.py .

RUN chmod +x setup.py; python3 -u setup.py --install; rm setup.py