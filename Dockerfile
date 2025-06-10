FROM mariadb:10.5

ENV MYSQL_ROOT_PASSWORD=password

COPY my.cnf /etc/mysql/conf.d/my.cnf
