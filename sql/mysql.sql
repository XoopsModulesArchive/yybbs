CREATE TABLE yybbs (
    id          INT(10)      NOT NULL AUTO_INCREMENT,
    serial      INT(10)      NOT NULL,
    bbs_id      TINYINT(3)   NOT NULL DEFAULT '1',
    uid         MEDIUMINT(5) NOT NULL DEFAULT '0',
    name        VARCHAR(64)           DEFAULT NULL,
    email       VARCHAR(64)           DEFAULT NULL,
    url         VARCHAR(64)           DEFAULT NULL,
    title       VARCHAR(64)           DEFAULT NULL,
    message     TEXT,
    icon        VARCHAR(24)  NOT NULL DEFAULT '',
    col         CHAR(8)      NOT NULL DEFAULT '0',
    passwd      VARCHAR(34)  NOT NULL DEFAULT '',
    parent      INT(10)      NOT NULL DEFAULT '0',
    inputdate   INT(10)      NOT NULL DEFAULT '0',
    update_date INT(10)      NOT NULL DEFAULT '0',
    ip          VARCHAR(22)  NOT NULL DEFAULT '',
    PRIMARY KEY (id)
)
    ENGINE = ISAM;

CREATE TABLE yybbs_faceicon (
    id       MEDIUMINT(5) DEFAULT '1' NOT NULL AUTO_INCREMENT,
    name     VARCHAR(32)              NOT NULL,
    icon     VARCHAR(30)  DEFAULT 0   NOT NULL,
    priority TINYINT(3)   DEFAULT '0' NOT NULL,
    bbs_id   TINYINT(3)   DEFAULT '1' NOT NULL,
    type     TINYINT(1)   DEFAULT 0   NOT NULL,
    PRIMARY KEY (id)
)
    ENGINE = ISAM;

CREATE TABLE yybbs_bbs (
    bbs_id       TINYINT(3)   DEFAULT '1'                                                               NOT NULL AUTO_INCREMENT,
    title        VARCHAR(64)  DEFAULT ''                                                                NOT NULL,
    page_limit   TINYINT(2)   DEFAULT '5'                                                               NOT NULL,
    ex           TEXT         DEFAULT ''                                                                NOT NULL,
    howto        TEXT         DEFAULT ''                                                                NOT NULL,
    color        VARCHAR(128) DEFAULT '#800000 #DF0000 #008040 #0000FF #C100C1 #FF80C0 #FF8040 #000080' NOT NULL,
    template_dir VARCHAR(64)                                                                            NOT NULL,
    windows_opt  VARCHAR(16)                                                                            NOT NULL,
    serial       INT(10)      DEFAULT 1                                                                 NOT NULL,
    xooops_use   TINYINT(1)   DEFAULT 1                                                                 NOT NULL,
    priority     TINYINT(2)   DEFAULT 0                                                                 NOT NULL,
    status       TINYINT(1)   DEFAULT 0                                                                 NOT NULL,
    PRIMARY KEY (bbs_id)
)
    ENGINE = ISAM;
