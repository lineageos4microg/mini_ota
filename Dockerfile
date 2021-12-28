FROM php:cli
LABEL maintainer="Simon Polack <spolack+git@mailbox.org>"

ENV ARCHIVE_PATH /srv
ENV ARCHIVE_URL https://download.lineage.org/
ENV PROP_TIMESTAMP_MARGIN 600


VOLUME ARCHIVE_PATH

EXPOSE 80

COPY ota.php ota.php

CMD ["php", "-S", "0.0.0.0:80", "ota.php"]
