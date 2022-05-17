#!/bin/bash
unset postgres_password
unset db_name

read -sp "Postgres user password (empty if not set): " postgres_password
echo
read -p "Database name: " db_name

# Populates the database
PGPASSWORD=$postgres_password psql -U postgres -d $db_name -a -f sql/model.sql
PGPASSWORD=$postgres_password psql -U postgres -d $db_name -a -f sql/data.sql

# Connect to the database
PGPASSWORD=$postgres_password psql -U postgres -d $db_name