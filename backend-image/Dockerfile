FROM webimp/php-56-apache
RUN apt-get update && DEBIAN_FRONTEND=noninteractive apt-get install -y ssmtp
RUN printf '[PHP]\ndate.timezone = "%s"\n' $TZ > /usr/local/etc/php/conf.d/tzone.ini
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
COPY ./php.ini /usr/local/etc/php
COPY ./ssmtp.conf /etc/ssmtp/ssmtp.conf
# COPY install-packages.sh .
# RUN ./install-packages.sh
# CMD /bin/sh -c "apt-get -y ping && ping mysql"
