http://yugmts.ru/

installation
------------
on production change password in deploy/sql/0-db.sql and in public/index.php lines 16 and 24

    xu@telemachus:~$ git clone https://github.com/boxfrommars/mtsbook.git
    xu@telemachus:~$ cd mtsbook
    xu@telemachus:~$ composer vendor update
    xu@telemachus:~$ ./deploy/deploy.sh
    xu@telemachus:~$ sudo su postgres
    postgres@telemachus:~$ psql
    postgres=# \i deploy/sql/0-db.sql
    postgres=# \q
    postgres@telemachus:~$ exit
    xu@telemachus:~$ psql -U mtsbook mtsbook
    mtsbook=# \i deploy/sql/1-schema.sql
    mtsbook=# \q

setup you vhost to public folder

usage
-----

admin -> /admin