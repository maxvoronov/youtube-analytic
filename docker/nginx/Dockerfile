FROM nginx:1.14
LABEL maintainer="Max Voronov"

ADD configs/nginx.conf /etc/nginx/nginx.conf
ADD vhosts/ /etc/nginx/sites-available/

ADD run.sh /run.sh
RUN chmod +x /run.sh

WORKDIR /var/www
CMD "/run.sh"