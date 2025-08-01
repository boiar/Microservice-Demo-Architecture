CREATE DATABASE IF NOT EXISTS `${MYSQL_DATABASE_REGISTRATION}`;

CREATE USER IF NOT EXISTS '${MYSQL_USER}'@'%' IDENTIFIED BY '${MYSQL_PASSWORD}';
GRANT ALL PRIVILEGES ON `${MYSQL_DATABASE_REGISTRATION}`.* TO '${MYSQL_USER}'@'%';
FLUSH PRIVILEGES;