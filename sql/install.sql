CREATE TABLE IF NOT EXISTS `{DB-PREFIX}seth8277_post` (
  `id`         INT(11)          NOT NULL,
  `user_id`    INT(10) UNSIGNED NOT NULL,
  `baiduid_id` INT(10) UNSIGNED NOT NULL,
  `post_url`   TINYTEXT         NOT NULL
  COMMENT '帖子链接',
  `starttime`  TIME             NOT NULL DEFAULT '08:00:00',
  `stoptime`   TIME             NOT NULL DEFAULT '24:00:00',
  `spacing`    TINYTEXT         NOT NULL
  COMMENT '间隔时间',
  `status`     TINYINT          NULL     DEFAULT 0,
  `nextdo`     DATETIME         NOT NULL DEFAULT '2017-01-01 00:00:00'
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;


CREATE TABLE IF NOT EXISTS `{DB-PREFIX}seth8277_post_content` (
  `id`      INT(11) NOT NULL,
  `post_id` INT(11) NOT NULL,
  `content` TEXT    NOT NULL
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

CREATE TABLE `{DB-PREFIX}seth8277_post_errors` (
  `id`      INT  NOT NULL,
  `message` TEXT NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = MyISAM
  CHARSET = utf8;


ALTER TABLE `{DB-PREFIX}seth8277_post`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `{DB-PREFIX}seth8277_post_content`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `{DB-PREFIX}seth8277_post`
  MODIFY `id` INT(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `{DB-PREFIX}seth8277_post_content`
  MODIFY `id` INT(11) NOT NULL AUTO_INCREMENT;
COMMIT;