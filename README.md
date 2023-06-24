### TODO

Applicatif:

    * rideshare frontend:
        - Finish integration (Frontpage assets & text, contact page, footer links,  legal page etc ..)
        - mobile friendly css (tailwind)
        - use nominatim container instead of backend proxy (perf issue)
        - socket.js utiliser VITE_LIVECHAT_URL ?

    * livechat feature:
        - chat channels (redis / socketio channels)

deployment:
    * automated CI pipeline (figure out database migration management here)
    * dev/staging & prod environment
    * Services configuration tuning (Nominatim, valhalla, postgres, redis, PHP, MySQL, ...)
    * valhalla: Add kubernetes persistent volume for custom_files
    * nominatim: 
        - Add persistent volume for IMPORT-FINISHED file
        - use sed in start.sh to rewrite password in init.sql