Install guide
========================

requires: php7, composer, docker

### install dependencies:
 
```
composer install
```

### run docker containers

```
docker run -d --name mariadb --net host -e MYSQL_ROOT_PASSWORD=pass mariadb:latest
docker run -d --name rabbit-management --net host rabbitmq:3-management
```

### create database

```
echo "CREATE DATABASE logs; CREATE USER 'test'@'127.0.0.1' IDENTIFIED BY 'test'; GRANT ALL PRIVILEGES ON logs.* TO 'test'@'127.0.0.1';" | mysql -h127.0.0.1 -uroot -ppass
```

### setup application:

```
./console/setup.php
```


### run application:

```
./console/mailer_consumer.php
./console/number_consumer.php
php -S 0.0.0.0:8080 -t web/
```
