-- ZerroCMS: Datenbank anlegen
-- In MySQL/phpMyAdmin ausführen (oder: mysql -u root -p < database/create_database.sql)

CREATE DATABASE IF NOT EXISTS zerrocms
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- Optional: Nutzer nur für diese DB (anstatt root)
-- CREATE USER IF NOT EXISTS 'zerrocms'@'localhost' IDENTIFIED BY 'dein-passwort';
-- GRANT ALL PRIVILEGES ON zerrocms.* TO 'zerrocms'@'localhost';
-- FLUSH PRIVILEGES;
