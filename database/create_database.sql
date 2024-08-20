To get started run the following SQL commands:

CREATE DATABASE DQMUA CHARACTER SET=utf8mb4;
CREATE USER 'dqmua'@'localhost' IDENTIFIED BY 'cY+lfxmb';
GRANT ALL ON DQMUA.* TO 'dqmua'@'localhost';
CREATE USER 'dqmua'@'127.0.0.1' IDENTIFIED BY 'cY+lfxmb';
GRANT ALL ON DQMUA.* TO 'dqmua'@'127.0.0.1';

USE DQMUA;

CREATE TABLE comment_ninecomments (
   comment_id INTEGER NOT NULL
     AUTO_INCREMENT,
   session_id VARCHAR(128),
   ip VARCHAR(128),
   support VARCHAR(11),
   name VARCHAR(200),
   comment VARCHAR(10000),
   subcomment INTEGER,
   posttime VARCHAR(128),
   PRIMARY KEY(comment_id),
   INDEX(session_id)
) ENGINE=InnoDB CHARSET=utf8mb4;



CREATE TABLE admin (
   admin_id INTEGER NOT NULL
     AUTO_INCREMENT,
   name VARCHAR(128),
   pass VARCHAR(128),
   PRIMARY KEY(admin_id),
   INDEX(name)
) ENGINE=InnoDB CHARSET=utf8mb4;


CREATE TABLE rockytalk (
   talk_id INTEGER NOT NULL
     AUTO_INCREMENT,
   image longblob,
   text VARCHAR(10000),
   name VARCHAR(128),
   postdate VARCHAR(128),
   app VARCHAR(128),
   PRIMARY KEY(talk_id)
) ENGINE=InnoDB CHARSET=utf8mb4;


CREATE TABLE comment_rockytalk (
   comment_id INTEGER NOT NULL
     AUTO_INCREMENT,
   session_id VARCHAR(128),
   ip VARCHAR(128),
   support VARCHAR(11),
   name VARCHAR(200),
   comment VARCHAR(10000),
   subcomment INTEGER,
   posttime VARCHAR(128),
   PRIMARY KEY(comment_id),
   INDEX(session_id),
   FOREIGN KEY (subcomment) REFERENCES rockytalk(talk_id)
   ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB CHARSET=utf8mb4;


CREATE TABLE comment_index (
   comment_id INTEGER NOT NULL
     AUTO_INCREMENT,
   session_id VARCHAR(128),
   ip VARCHAR(128),
   support VARCHAR(11),
   name VARCHAR(200),
   comment VARCHAR(10000),
   subcomment INTEGER,
   posttime VARCHAR(128),
   PRIMARY KEY(comment_id),
   INDEX(session_id)
) ENGINE=InnoDB CHARSET=utf8mb4;