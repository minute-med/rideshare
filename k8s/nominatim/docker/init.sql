SELECT 'CREATE ROLE "www-data" LOGIN PASSWORD ''he9ffej80w'''
WHERE NOT EXISTS (SELECT FROM pg_catalog.pg_roles WHERE  rolname = 'www-data')\gexec