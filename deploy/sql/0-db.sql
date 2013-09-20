-- in db postgres with postgres user
\c postgres
DROP DATABASE IF EXISTS mtsbook;
DROP ROLE IF EXISTS mtsbook;
CREATE ROLE mtsbook ENCRYPTED PASSWORD 'mtsbook' LOGIN;
CREATE DATABASE mtsbook OWNER mtsbook;
GRANT ALL ON DATABASE mtsbook TO mtsbook;
\c mtsbook