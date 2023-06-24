#!/bin/bash -ex

tailpid=0
replicationpid=0

stopServices() {
  service apache2 stop
  kill $replicationpid
  kill $tailpid
}
trap stopServices SIGTERM TERM INT

/app/config.sh

if id nominatim >/dev/null 2>&1; then
  echo "user nominatim already exists"
else
  useradd -m -p ${NOMINATIM_PASSWORD} nominatim
fi


# config .pgpassfile for nominatim import which use psql
cp $PGPASSFILE /home/nominatim/
chown nominatim:nominatim /home/nominatim/.pgpass
chmod 0600 /home/nominatim/.pgpass
export PGPASSFILE=/home/nominatim/.pgpass

IMPORT_FINISHED=/app/import-finished

if [ ! -f ${IMPORT_FINISHED} ]; then
  /app/init.sh
  touch ${IMPORT_FINISHED}
else
  chown -R nominatim:nominatim ${PROJECT_DIR}
fi

cd ${PROJECT_DIR} && sudo -E -u nominatim nominatim refresh --website --functions

service apache2 start

# start continous replication process
if [ "$REPLICATION_URL" != "" ] && [ "$FREEZE" != "true" ]; then
  # run init in case replication settings changed
  sudo -E -u nominatim nominatim replication --project-dir ${PROJECT_DIR} --init
  if [ "$UPDATE_MODE" == "continuous" ]; then
    echo "starting continuous replication"
    sudo -E -u nominatim nominatim replication --project-dir ${PROJECT_DIR} &> /var/log/replication.log &
    replicationpid=${!}
  elif [ "$UPDATE_MODE" == "once" ]; then
    echo "starting replication once"
    sudo -E -u nominatim nominatim replication --project-dir ${PROJECT_DIR} --once &> /var/log/replication.log &
    replicationpid=${!}
  elif [ "$UPDATE_MODE" == "catch-up" ]; then
    echo "starting replication once in catch-up mode"
    sudo -E -u nominatim nominatim replication --project-dir ${PROJECT_DIR} --catch-up &> /var/log/replication.log &
    replicationpid=${!}
  else
    echo "skipping replication"
  fi
fi

if [ "$REVERSE_ONLY" = "true" ]; then
  echo "Warm database caches for reverse queries"
  sudo -E -u nominatim nominatim admin --warm --reverse > /dev/null
else
  echo "Warm database caches for search and reverse queries"
  sudo -E -u nominatim nominatim admin --warm > /dev/null
fi
echo "Warming finished"

echo "--> Nominatim is ready to accept requests"

tail -f /var/log/apache2/error.log