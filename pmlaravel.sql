DROP TABLE IF EXISTS `5users`;
CREATE TABLE `5users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `a` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `c` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=169 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 
INSERT INTO `5users` VALUES ('137', 'webdaud@gmail.com', 'webdaud@gmail.com', '$2y$10$aY0R/bBj.I6YRjFznlPnBu.00RgQ84OPqTBymYQk4mlkD0QGIR3vO', '089522847981', null, null, 'tBqLziz6Ax8mEGT9yx8KfwPpeGwsQvdOoWa07OBgL99w1XLKzVnWr4aXBjcs', null, null);
INSERT INTO `5users` VALUES ('157', 'Akun 1', 'Akun 1', '$2y$10$Qnf7r998HYtZCLgZhFnUzOlX0QZ6HY7/k9QH739UMxq0yoe5jwws2', null, '586484', null, null, null, null);
INSERT INTO `5users` VALUES ('158', 'Akun 2', 'Akun 2', '$2y$10$Jy4F2CcHeKDDMdK4Riv6nutHazBv6Xv1SBIhfuyeUKgBL54lnifUy', null, '651918', null, null, null, null);
INSERT INTO `5users` VALUES ('159', 'Akun 3', 'Akun 3', '$2y$10$jq2.zFTi1w/sd5hNv3s83eAK6qvIWAO1aV1mW7Hj2wlnw1FrkmXK6', null, '360042', null, null, null, null);
INSERT INTO `5users` VALUES ('160', 'Akun 4', 'Akun 4', '$2y$10$uiQKJZiQdv7wP.hYqJfvFemaf9JTlNbCeKxiJZEhXHhRbkZ1BHOU.', null, '293476', null, null, null, null);
INSERT INTO `5users` VALUES ('161', 'Akun 5', 'Akun 5', '$2y$10$BqVm3h/5e71d2Q97tqilhOOoBB55d0bfMqrNPvhenkeaQVk1Fv6.u', null, '105771', null, null, null, null);
INSERT INTO `5users` VALUES ('162', 'Akun 6', 'Akun 6', '$2y$10$0aYWhRDi/WG8DmetBvQrK.lobmnpJtt4w3tFq45MXu18MTOjPkySG', null, '130436', null, null, null, null);
INSERT INTO `5users` VALUES ('163', 'Akun 7', 'Akun 7', '$2y$10$.3ae5NanIuboLv1MUHkrheTur9T/L/kjQtx8mgr89LIEVPX7keB32', null, '199025', null, null, null, null);
INSERT INTO `5users` VALUES ('164', 'Akun 8', 'Akun 8', '$2y$10$edFXiiSo7uXRFMfIPVa4D.2rGMcDRhFVUjFGb7a2gZkgWkFxWiVta', null, '360530', null, null, null, null);
INSERT INTO `5users` VALUES ('165', 'Akun 9', 'Akun 9', '$2y$10$8Gmw2lYn0eZSY7aJV/JLg.UH.awOk9sX75AA7HZ4yN76dcnBQbPSG', null, '513226', null, null, null, null);
INSERT INTO `5users` VALUES ('166', 'Akun 10', 'Akun 10', '$2y$10$oQ5dhqIZWXEDQim3LcaziuLHCsuzHmQwbXyKjfm8hstAX1UN.55xa', null, '529719', null, null, null, null);
INSERT INTO `5users` VALUES ('168', 'Akun 11', 'Akun 11', '$2y$10$9YC52I/a8ljh0zzmeqNjE.vKlXZV0A8THa5xl5IAnuqvIvGI230xm', null, '345858', null, null, null, null);
