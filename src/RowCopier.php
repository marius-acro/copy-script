<?php

namespace RowCopier;

require_once "DatabaseManipulator.php";

use mysqli;

class RowCopier {
    public function copyRow(
        int $feedId,
        ?string $only = null,
        ?int $postCount = null
    ): void {
        $connection = DatabaseManipulator::getDatabaseConnection();

        $this->copyFeedsRow($connection, $feedId);

        if ($only === 'instagram') {
            $this->copyInstagramRow($connection, $feedId);
        } elseif ($only === 'tiktok') {
            $this->copyTiktokRow($connection, $feedId);
        } else {
            $this->copyInstagramRow($connection, $feedId);
            $this->copyTiktokRow($connection, $feedId);
        }

        if(!is_null($postCount)) {
            $this->copyPostRows($connection, $feedId, $postCount);
        }

        echo 'Copying successful!';

        $connection->close();
    }

    public function copyFeedsRow(mysqli $connection, int $feedId): void
    {
        // Fetch the row which we want to copy.
        $statement = $connection->prepare('SELECT * FROM feeds WHERE id = ?');
        $statement->bind_param('i', $feedId);
        $statement->execute();
        $row = $statement->get_result()->fetch_assoc();

        // Insert the values of the row we fetched.
        $statement = $connection->prepare("INSERT INTO feeds_copy VALUES (?, ?)");
        $statement->bind_param("is", $row['id'], $row['name']);
        $statement->execute();
    }

    public function copyInstagramRow(mysqli $connection, int $feedId): void
    {
        // Fetch the row which we want to copy.
        $statement = $connection->prepare('SELECT * FROM instagram_sources WHERE feed_id = ?');
        $statement->bind_param('i', $feedId);
        $statement->execute();
        $row = $statement->get_result()->fetch_assoc();

        // Insert the values of the row we fetched.
        $statement = $connection->prepare("INSERT INTO instagram_sources_copy VALUES (?, ?, ?, ?)");
        $statement->bind_param("iisi", $row['id'], $row['feed_id'], $row['name'], $row['fan_count']);
        $statement->execute();
    }

    public function copyTiktokRow(mysqli $connection, int $feedId): void
    {
        // Fetch the row which we want to copy.
        $statement = $connection->prepare('SELECT * FROM tiktok_sources WHERE feed_id = ?');
        $statement->bind_param('i', $feedId);
        $statement->execute();
        $row = $statement->get_result()->fetch_assoc();

        // Insert the values of the row we fetched.
        $statement = $connection->prepare("INSERT INTO tiktok_sources_copy VALUES (?, ?, ?, ?)");
        $statement->bind_param("iisi", $row['id'], $row['feed_id'], $row['name'], $row['fan_count']);
        $statement->execute();
    }

    public function copyPostRows(mysqli $connection, int $feedId, int $postCount): void
    {
        // Fetch the rows which we want to copy.
        $statement = $connection->prepare('SELECT * FROM posts WHERE feed_id = ? LIMIT ?');
        $statement->bind_param('ii', $feedId, $postCount);
        $statement->execute();
        $rows = $statement->get_result()->fetch_all(MYSQLI_ASSOC);

        foreach($rows as $row) {
            // Insert the values of the row we fetched.
            $statement = $connection->prepare("INSERT INTO posts_copy VALUES (?, ?, ?)");
            $statement->bind_param("iis", $row['id'], $row['feed_id'], $row['url']);
            $statement->execute();
        }
    }
}
