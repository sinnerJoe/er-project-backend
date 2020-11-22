FROM webimp/php-56-apache

RUN printf '[PHP]\ndate.timezone = "%s"\n' $TZ > /usr/local/etc/php/conf.d/tzone.ini
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# COPY install-packages.sh .
# RUN ./install-packages.sh
# CMD /bin/sh -c "apt-get -y ping && ping mysql"