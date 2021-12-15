FROM node:12.18.3

RUN apt-get update && apt-get install -y libglu1 && npm i -g gulp@4.0.2 && npm i -g webpack@5.9.0 && npm i -g webpack-cli@4.2.0

RUN npm i -g grunt-cli

WORKDIR /var/www

CMD /bin/bash -c "tail -f /dev/null"
