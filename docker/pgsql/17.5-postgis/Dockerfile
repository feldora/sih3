FROM postgres:17.5

RUN apt-get update \
   && apt-get install -y \
      postgresql-17-postgis-3 \
      postgresql-17-postgis-3-scripts \
   && rm -rf /var/lib/apt/lists/*
# COPY initdb-postgis.sh /docker-entrypoint-initdb.d/
# RUN chmod +x /docker-entrypoint-initdb.d/initdb-postgis.sh
# RUN docker-entrypoint-initdb.d/initdb-postgis.sh