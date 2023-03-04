<?php

namespace RowCopier;

use mysqli;

class DatabaseManipulator {
    public static function getDatabaseConnection(): mysqli
    {
        $hostname = '127.0.0.1';
        $username = 'root';
        $password = 'password';
        $database = 'influencer_database';

        return new mysqli($hostname, $username, $password, $database);
    }

    public static function createTables(): void
    {
        $connection = self::getDatabaseConnection();

        $sql = 'CREATE TABLE `feeds` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;';
        $connection->query($sql);

        $sql = 'CREATE TABLE `feeds_copy` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;';
        $connection->query($sql);

        $sql = 'CREATE TABLE `instagram_sources` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `feed_id` bigint(20) NOT NULL,
            `name` varchar(255) NOT NULL,
            `fan_count` int(11) NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;';
        $connection->query($sql);

        $sql = 'CREATE TABLE `instagram_sources_copy` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `feed_id` bigint(20) NOT NULL,
            `name` varchar(255) NOT NULL,
            `fan_count` int(11) NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;';
        $connection->query($sql);

        $sql = 'CREATE TABLE `posts` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `feed_id` bigint(20) DEFAULT NULL,
            `url` varchar(255) DEFAULT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;';
        $connection->query($sql);

        $sql = 'CREATE TABLE `posts_copy` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `feed_id` bigint(20) DEFAULT NULL,
            `url` varchar(255) DEFAULT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;';
        $connection->query($sql);

        $sql = 'CREATE TABLE `tiktok_sources` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `feed_id` bigint(20) NOT NULL,
            `name` varchar(255) NOT NULL,
            `fan_count` int(11) NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;';
        $connection->query($sql);

        $sql = 'CREATE TABLE `tiktok_sources_copy` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `feed_id` bigint(20) NOT NULL,
            `name` varchar(255) NOT NULL,
            `fan_count` int(11) NOT NULL,
            PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;';
        $connection->query($sql);

        $connection->close();
    }

    public static function truncateTables(): void
    {
        $connection = self::getDatabaseConnection();

        $tables = [
            'feeds',
            'feeds_copy',
            'instagram_sources',
            'instagram_sources_copy',
            'posts',
            'posts_copy',
            'tiktok_sources',
            'tiktok_sources_copy',
        ];

        foreach ($tables as $table) {
            $sql = "TRUNCATE TABLE " . $table;
            $connection->query($sql);
        }

        $connection->close();
    }

    public static function seedDummyData(): void
    {
        $connection = self::getDatabaseConnection();

        $sql = "INSERT INTO `feeds` (id, name) VALUES
            (1, 'Aelroy Influencer'),
            (2, 'Ismail Influencer'),
            (3, 'Marius Influencer')
        ";
        $connection->query($sql);

        $sql = "INSERT INTO `instagram_sources` (id, feed_id, name, fan_count) VALUES
            (1, 1, '@aelroy.influencer', 30),
            (2, 2, '@ismail.influencer', 20),
            (3, 3, '@marius.influencer', 10)
        ";
        $connection->query($sql);

        $sql = "INSERT INTO `posts` (id, feed_id, url) VALUES
            (1, 1, 'Aelroys first URL'),
            (2, 1, 'Aelroys second URL'),
            (3, 1, 'Aelroys third URL'),
            (4, 1, 'Aelroys fourth URL'),
            (5, 1, 'Aelroys fifth URL'),

            (6, 2, 'Ismails first URL'),
            (7, 2, 'Ismails second URL'),
            (8, 2, 'Ismails third URL'),
            (9, 2, 'Ismails fourth URL'),
            (10, 2, 'Ismails fifth URL'),

            (11, 3, 'Marius first URL'),
            (12, 3, 'Marius second URL'),
            (13, 3, 'Marius third URL'),
            (14, 3, 'Marius fourth URL'),
            (15, 3, 'Marius fifth URL')
        ";
        $connection->query($sql);

        $sql = "INSERT INTO `tiktok_sources` (id, feed_id, name, fan_count) VALUES
            (1, 1, '@aelroy_influencer', 60),
            (2, 2, '@ismail_influencer', 40),
            (3, 3, '@marius_influencer', 20)
        ";
        $connection->query($sql);

        $connection->close();
    }
}
