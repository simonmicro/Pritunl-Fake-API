FROM goofball222/pritunl:latest

ENV ORIG_API_SERVER app.pritunl.com
ENV FAKE_API_SERVER pritunl-api.simonmicro.de
ENV ORIG_AUTH_SERVER auth.pritunl.com
ENV FAKE_AUTH_SERVER pritunl-api.simonmicro.de\\/auth

# This is basically the classic installer script...
RUN ORIG_API_SERVER_ESCAPED=$(echo "$ORIG_API_SERVER" | sed -e 's/\./\\./g') && \
    FAKE_API_SERVER_ESCAPED=$(echo "$FAKE_API_SERVER" | sed -e 's/\./\\./g') && \
    ORIG_AUTH_SERVER_ESCAPED=$(echo "$ORIG_AUTH_SERVER" | sed -e 's/\./\\./g') && \
    FAKE_AUTH_SERVER_ESCAPED=$(echo "$FAKE_AUTH_SERVER" | sed -e 's/\./\\./g') && \
    echo 'Rewriting pritunl api endpoint: libs' && \
    find /usr/lib/python2.7 -type f -print0 | xargs -0 sed -i "s/$ORIG_API_SERVER_ESCAPED/$FAKE_API_SERVER_ESCAPED/g" && \
    echo 'Rewriting pritunl api endpoint: www' && \
    find /usr/share/pritunl/www/ -type f -print0 | xargs -0 sed -i "s/$ORIG_API_SERVER_ESCAPED/$FAKE_API_SERVER_ESCAPED/g" && \
    echo 'Rewriting pritunl auth (SSO) endpoint: libs+www' && \
    find /usr/lib/python2.7 -type f -print0 | xargs -0 sed -i "s/$ORIG_AUTH_SERVER_ESCAPED/$FAKE_AUTH_SERVER_ESCAPED/g" && \
    find /usr/share/pritunl/www/ -type f -print0 | xargs -0 sed -i "s/$ORIG_AUTH_SERVER_ESCAPED/$FAKE_AUTH_SERVER_ESCAPED/g" && \
    echo 'Done...'
