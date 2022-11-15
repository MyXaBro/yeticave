DROP DATABASE IF EXISTS yeticave;

CREATE DATABASE yeticave
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;

USE yeticave;

CREATE table category(
                         id INT AUTO_INCREMENT PRIMARY Key,
                         character_code VARCHAR(128) UNIQUE,
                         name_category VARCHAR(128)
);

CREATE table users(
                      id INT AUTO_INCREMENT PRIMARY Key,
                      data_registration TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                      email VARCHAR (256) NOT NULL UNIQUE,
                      user_name VARCHAR(128) UNIQUE,
                      user_password VARCHAR (256),
                      contacts TEXT
);

CREATE TABLE yeticave.lots(
                              id INT AUTO_INCREMENT PRIMARY KEY,
                              data_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                              names_lot VARCHAR (255),
                              category_id INT,
                              description TEXT,
                              start_price INT,
                              image VARCHAR (255),
                              time_finished DATE,
                              step INT,
                              user_id INT,
                              winner_id INT,
                              FOREIGN KEY (user_id) REFERENCES users(id),
                              FOREIGN KEY (winner_id) REFERENCES users(id),
                              FOREIGN KEY (category_id) REFERENCES category(id),
                              FULLTEXT (names_lot, description)

);

CREATE TABLE yeticave.bets(
                              id INT AUTO_INCREMENT PRIMARY KEY,
                              data_bet TIMESTAMP DEFAULT CURRENT_TIMESTAMP ,
                              price_bet INT,
                              user_id INT,
                              lot_id INT,
                              FOREIGN KEY (user_id) REFERENCES users(id),
                              FOREIGN KEY (lot_id) REFERENCES lots(id)
);

ALTER TABLE lots ADD FULLTEXT lots_search(names_lot,description);