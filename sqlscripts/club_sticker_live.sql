USE club_sticker;

CREATE TABLE users (
  userID           VARCHAR(100) NOT NULL PRIMARY KEY, -- email address of user
  firstName        VARCHAR(20)  NOT NULL,
  lastName         VARCHAR(20)  NOT NULL,
  phone			       VARCHAR(15)  NOT NULL, 
  password         VARCHAR(255) NOT NULL,
  description	     VARCHAR(255) DEFAULT NULL,
  profilePicture   VARCHAR(100) DEFAULT NULL
);

CREATE TABLE failed_logins (
    userID		     VARCHAR(100) NOT NULL PRIMARY KEY,
    count		       INT,
    lastLoginTime  INT
 );

CREATE TABLE products (
  productID         INT            NOT NULL   AUTO_INCREMENT,
  productCode      VARCHAR(100)   NOT NULL, -- product code is the image filename.
  productName       VARCHAR(30)    NOT NULL,
  listPrice         DECIMAL(10,2)  NOT NULL,
  uploader			    VARCHAR(100)   DEFAULT NULL,
  PRIMARY KEY (productID), 
  FOREIGN KEY (uploader) REFERENCES users(userID)
);


INSERT INTO products (productID, productCode, productName, listPrice) VALUES 
(1, 'boba', 'Boba Tea', '2.59'),
(2, 'offline-dinosaur', 'Dinosaur', '3.29'),
(3, 'dog', 'Lab', '2.49'),
(4, 'patrick', 'Patrick Star', '3.00'),
(5, 'lofi', 'Lofi Girl', '2.47'),
(6, 'cosmic-sound', 'Cosmic Sounds', '3.29'),
(7, 'calitravel', 'Coastal Cali', 2.49);