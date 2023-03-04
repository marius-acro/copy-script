<?php

use PHPUnit\Framework\TestCase;
use RowCopier\DatabaseManipulator;
use RowCopier\RowCopier;

class RowCopierTest extends TestCase {
    public function setUp(): void
    {
        DatabaseManipulator::seedDummyData();
    }

    public function tearDown(): void
    {
        DatabaseManipulator::truncateTables();
    }

    public function testTheFeedsRowIsCopiedCorrectly()
    {
        $randomIdToTest = $this->getRandomIdToTest();

        $rowCopier = new RowCopier();
        $rowCopier->copyFeedsRow(
            DatabaseManipulator::getDatabaseConnection(),
            $randomIdToTest
        );

        $copiedRow = $this->fetchSingleRowWithId('feeds_copy', $randomIdToTest);

        $expectedName = match($randomIdToTest) {
            1 => 'Aelroy Influencer',
            2 => 'Ismail Influencer',
            3 => 'Marius Influencer',
        };

        $this->assertEquals($randomIdToTest, $copiedRow['id']);
        $this->assertEquals($expectedName, $copiedRow['name']);
    }

    public function testTheInstagramRowIsCopiedCorrectly()
    {
        $randomIdToTest = $this->getRandomIdToTest();

        $rowCopier = new RowCopier();
        $rowCopier->copyInstagramRow(
            DatabaseManipulator::getDatabaseConnection(),
            $randomIdToTest
        );

        $copiedRow = $this->fetchSingleRowWithId('instagram_sources_copy', $randomIdToTest);

        $expectedName = match($randomIdToTest) {
            1 => '@aelroy.influencer',
            2 => '@ismail.influencer',
            3 => '@marius.influencer',
        };

        $expectedFanCount = match($randomIdToTest) {
            1 => 30,
            2 => 20,
            3 => 10,
        };

        $this->assertEquals($randomIdToTest, $copiedRow['id']);
        $this->assertEquals($randomIdToTest, $copiedRow['feed_id']);
        $this->assertEquals($expectedName, $copiedRow['name']);
        $this->assertEquals($expectedFanCount, $copiedRow['fan_count']);
    }

    public function testTheTiktokRowIsCopiedCorrectly()
    {
        $randomIdToTest = $this->getRandomIdToTest();

        $rowCopier = new RowCopier();
        $rowCopier->copyTiktokRow(
            DatabaseManipulator::getDatabaseConnection(),
            $randomIdToTest
        );

        $copiedRow = $this->fetchSingleRowWithId('tiktok_sources_copy', $randomIdToTest);

        $expectedName = match($randomIdToTest) {
            1 => '@aelroy_influencer',
            2 => '@ismail_influencer',
            3 => '@marius_influencer',
        };

        $expectedFanCount = match($randomIdToTest) {
            1 => 60,
            2 => 40,
            3 => 20,
        };

        $this->assertEquals($randomIdToTest, $copiedRow['id']);
        $this->assertEquals($randomIdToTest, $copiedRow['feed_id']);
        $this->assertEquals($expectedName, $copiedRow['name']);
        $this->assertEquals($expectedFanCount, $copiedRow['fan_count']);
    }

    public function testThePostsRowsAreCopiedCorrectly()
    {
        $randomIdToTest = $this->getRandomIdToTest();
        $randomPostCount = rand(1, 5);

        $rowCopier = new RowCopier();
        $rowCopier->copyPostRows(
            DatabaseManipulator::getDatabaseConnection(),
            $randomIdToTest,
            $randomPostCount,
        );

        $copiedRows = $this->fetchMultipleRowsWithId('posts_copy', $randomIdToTest, $randomPostCount);

        $expectedName = match($randomIdToTest) {
            1 => 'Aelroy',
            2 => 'Ismail',
            3 => 'Marius',
        };

        $this->assertCount($randomPostCount, $copiedRows);

        foreach($copiedRows as $copiedRow) {
            // These asserts could be fleshed out more.
            $this->assertEquals($randomIdToTest, $copiedRow['feed_id']);
            $this->assertStringContainsString($expectedName, $copiedRow['url']);
        }
    }

    /**
     * Readability definitely suffers a bit here, but it should only serve as an example.
     * Normally I would also break that up into multiple tests, similar to the unit tests.
     */
    public function testIntegrationTestExample()
    {
        $randomIdToTest = $this->getRandomIdToTest();
        $randomSocialMediaPlatform = $this->getRandomSocialMediaPlatform();
        $randomPostCount = rand(1, 5);

        $scriptArguments = [
            $randomIdToTest,
            '--only=' . $randomSocialMediaPlatform,
            '--include-posts=' . $randomPostCount,
        ];
        shuffle($scriptArguments);
        $scriptArgumentsAsString = implode(' ', $scriptArguments);

        exec('php src/script.php ' . $scriptArgumentsAsString);

        // Assert that the feeds row has been copied correctly.
        $copiedFeedsRow = $this->fetchSingleRowWithId('feeds_copy', $randomIdToTest);

        $expectedName = match($randomIdToTest) {
            1 => 'Aelroy Influencer',
            2 => 'Ismail Influencer',
            3 => 'Marius Influencer',
        };

        $this->assertEquals($randomIdToTest, $copiedFeedsRow['id']);
        $this->assertEquals($expectedName, $copiedFeedsRow['name']);

        // Assert that the social media source row has been copied correctly.
        $copiedSocialMediaRow = $this->fetchSingleRowWithId($randomSocialMediaPlatform . '_sources_copy', $randomIdToTest);

        // I simplified the asserts here for readability, since detailed asserts are in the unit test anyway.
        $this->assertEquals($randomIdToTest, $copiedSocialMediaRow['id']);
        $this->assertEquals($randomIdToTest, $copiedSocialMediaRow['feed_id']);

        // Assert that the other social media source has not been copied.
        $socialMediaThatShouldNotBeCopied = match($randomSocialMediaPlatform) {
            'tiktok' => 'instagram',
            'instagram' => 'tiktok',
        };

        $socialMediaRowThatShouldNotBeCopied = $this->fetchSingleRowWithId($socialMediaThatShouldNotBeCopied . '_sources_copy', $randomIdToTest);
        $this->assertEmpty($socialMediaRowThatShouldNotBeCopied);

        $copiedPostsRows = $this->fetchMultipleRowsWithId('posts_copy', $randomIdToTest, $randomPostCount);

        $expectedName = match($randomIdToTest) {
            1 => 'Aelroy',
            2 => 'Ismail',
            3 => 'Marius',
        };

        $this->assertCount($randomPostCount, $copiedPostsRows);

        foreach($copiedPostsRows as $copiedRow) {
            $this->assertEquals($randomIdToTest, $copiedRow['feed_id']);
            $this->assertStringContainsString($expectedName, $copiedRow['url']);
        }
    }

    private function fetchSingleRowWithId(string $tableName, int $id): array
    {
        $connection = DatabaseManipulator::getDatabaseConnection();
        $sql = 'SELECT * FROM ' . $tableName . ' WHERE id = ' . $id;
        $result = $connection->query($sql);

        $connection->close();

        return $result->fetch_assoc() ?? [];
    }

    private function fetchMultipleRowsWithId(string $tableName, int $id, int $rowCount): array
    {
        $connection = DatabaseManipulator::getDatabaseConnection();
        $sql = 'SELECT * FROM ' . $tableName . ' WHERE feed_id = ' . $id . ' LIMIT ' . $rowCount;
        $result = $connection->query($sql);

        $connection->close();

        return $result->fetch_all(MYSQLI_ASSOC) ?? [];
    }

    private function getRandomIdToTest(): int
    {
        $availableIds = [1, 2, 3];

        return $availableIds[array_rand($availableIds)];
    }

    private function getRandomSocialMediaPlatform(): string
    {
        $availablePlatforms = ['instagram', 'tiktok'];

        return $availablePlatforms[array_rand($availablePlatforms)];
    }
}