ALTER TABLE `shop_order_detail` ADD `rfid` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '기여사이트(레퍼러)코드' after oid;
ALTER TABLE `shop_order_detail` ADD `kwid` INT(5) UNSIGNED ZEROFILL NOT NULL COMMENT '검색키워드 코드' after rfid;
ALTER TABLE `shop_order_detail` ADD `cid` VARCHAR(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '카테고리 코드' after order_from;