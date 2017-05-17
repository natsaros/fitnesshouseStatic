CREATE TABLE AK_SETTINGS (
  ID     BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  SKEY   VARCHAR(250)        NOT NULL DEFAULT '',
  SVALUE LONGTEXT
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

CREATE TABLE AK_USERS (
  ID                BIGINT(20) UNSIGNED NOT NULL    AUTO_INCREMENT PRIMARY KEY,
  NAME              VARCHAR(250)        NOT NULL    DEFAULT '',
  PASSWORD          VARCHAR(255)        NOT NULL,
  FIRST_NAME        VARCHAR(250)        NOT NULL    DEFAULT '',
  LAST_NAME         VARCHAR(250)        NOT NULL    DEFAULT '',
  EMAIL             VARCHAR(100)                    DEFAULT '',
  PHONE             VARCHAR(30)                     DEFAULT '',
  LINK              VARCHAR(250)                    DEFAULT '',
  GENDER            VARCHAR(50)                     DEFAULT '',
  PICTURE           VARCHAR(250)                    DEFAULT '',
  USER_STATUS       INT(11)             NOT NULL    DEFAULT 0,
  IS_ADMIN          INT(11)             NOT NULL    DEFAULT 0,
  ACTIVATION_DATE   DATETIME            NOT NULL,
  MODIFICATION_DATE DATETIME            NOT NULL    DEFAULT CURRENT_TIMESTAMP
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;


CREATE TABLE AK_USER_META (
  ID         BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  USER_ID    BIGINT(20) UNSIGNED NOT NULL DEFAULT 0,
  META_KEY   VARCHAR(255)                 DEFAULT NULL,
  META_VALUE LONGTEXT
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

ALTER TABLE AK_USER_META
  ADD INDEX USER_IND (USER_ID);

ALTER TABLE AK_USER_META
  ADD CONSTRAINT AK_USER_META_IBFK_1 FOREIGN KEY (USER_ID) REFERENCES AK_USERS (ID)
  ON DELETE CASCADE;

INSERT INTO AK_USERS (ID, NAME, PASSWORD, FIRST_NAME, LAST_NAME, EMAIL, PHONE, LINK, GENDER, PICTURE, USER_STATUS, IS_ADMIN)
VALUES
  (1, 'admin', '$2y$10$6g91rGCWuZ9zbiJV2YDzeOgmxlyCKauJejWUVJtWPJirKngbSeyVu', 'admin', 'admin', 'admin@admin.gr', '',
      '', '', '', 1, 1);

CREATE TABLE AK_POSTS (
  ID                BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  TITLE             VARCHAR(255),
  ACTIVATION_DATE   DATETIME            NOT NULL,
  MODIFICATION_DATE DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
  STATE             INT(11)                      DEFAULT 0,
  USER_ID           BIGINT(20) UNSIGNED
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

ALTER TABLE AK_POSTS
  ADD CONSTRAINT AK_POSTS_FK FOREIGN KEY (USER_ID) REFERENCES AK_USERS (ID)
  ON DELETE CASCADE;

ALTER TABLE AK_POSTS
  ADD INDEX USER_P_IND (USER_ID);

CREATE TABLE AK_POST_DETAILS (
  ID         BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  POST_ID    BIGINT(20) UNSIGNED NOT NULL,
  SEQUENCE   INT,
  TEXT       LONGTEXT,
  IMAGE_PATH VARCHAR(255),
  IMAGE      BLOB
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

ALTER TABLE AK_POST_DETAILS
  ADD CONSTRAINT AK_PD_FK FOREIGN KEY (POST_ID) REFERENCES AK_POSTS (ID)
  ON DELETE CASCADE;

ALTER TABLE AK_POST_DETAILS
  ADD INDEX POST_IND (POST_ID);


CREATE TABLE AK_COMMENTS (
  ID      BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  COMMENT LONGTEXT,
  DATE    DATETIME                     DEFAULT CURRENT_TIMESTAMP,
  STATE   INT(11)                      DEFAULT 0,
  USER_ID BIGINT(20) UNSIGNED,
  POST_ID BIGINT(20) UNSIGNED

)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8;

ALTER TABLE AK_COMMENTS
  ADD CONSTRAINT AK_CP_FK FOREIGN KEY (POST_ID) REFERENCES AK_POSTS (ID)
  ON DELETE CASCADE;


ALTER TABLE AK_COMMENTS
  ADD CONSTRAINT AK_CU_FK FOREIGN KEY (USER_ID) REFERENCES AK_USERS (ID)
  ON DELETE CASCADE;

ALTER TABLE AK_COMMENTS
  ADD INDEX POST_C_IND (POST_ID);

ALTER TABLE AK_COMMENTS
  ADD INDEX USER_C_IND (USER_ID);