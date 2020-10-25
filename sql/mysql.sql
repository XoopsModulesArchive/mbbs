CREATE TABLE `mbbs` (
    `mbbs_id`   INT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    `mbbs_uid`  INT(8)          NOT NULL DEFAULT '0',
    `mbbs_name` VARCHAR(100)             DEFAULT NULL,
    `mbbs_text` TEXT            NOT NULL,
    `mbbs_time` INT(10)         NOT NULL DEFAULT '0',
    PRIMARY KEY (`mbbs_id`)
);
