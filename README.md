# Setup
## Prerequisites
- Composer
- Docker (for the database)
- PHP >= 8
- Port `3306` should be free. There might be conflicts with other MySQL/MariaDB installations you have locally

## Getting it up and running
- Run `composer install`
- Run `docker-compose up -d` (or `docker compose up -d`, depends on your docker installation) in the root directory to get the MariaDB database up and running in the background
- In `script.php`, you should un-comment the call to `initializeDatabase()` to enable the creation of the database schema when running the script for the first time. Comment it again after the first run. You can also un-comment `refreshDatabase()` for cleanups between script runs
- Now you can run e.g. `php script.php 2` to copy the entries with the ID `2`, or play around with the command line options defined in `assignment.pdf`
- You can run the tests by using `composer test`

## Cleanup
- I would suggest running `docker-compose down` (or `docker compose down`, depends on your docker installation) to clean up the Docker image when you are done. That could otherwise lead to a leftover container which can be really annoying to get rid of once the original `docker-compose.yml` has been removed.

## Some general notes
- If you can not get it running for some reason, I have a screenshot in the screenshots directory that shows the green tests. If necessary I can also provide a screencast showing the script in action.
- Working without a framework was the most significant hurdle for me. I have never done that in my professional career and it brought some interesting challenges with it. I actually considered bootstrapping a whole Laravel application for this task, because I might have been quicker that way (since I'm comfortable in Laravel) and produced a better result. But I figured that by doing that, I would be avoiding what you actually wanted me to do, so I chose not to do it.
- Some requirements were not completely clear to me. E.g. it is written that posts has a 1:n relationship to feeds, but common logic and the rest of the document indicate the opposite (that feeds has a 1:n relationship to posts). In real life, I would have cleared up ambiguous requirements like that, but here I chose to do what seemed correct to me. Maybe I got something wrong as a result, but that was fine for me since it's only a sample project.
- You could obviously make this script better, safer, more flexible, cleaner in a myriad of ways. But I didn't want to spend forever on it and instead focused on reaching the stated goal (while still providing you with a way to test it out for yourself, although that wasn't mentioned as requirement).
- I used `setUp()` and `tearDown()` to deal with the database state in the tests. That works fine for now, but doesn't scale well. Normally you would handle that in a much more elegant way, e.g. by using Factories and the `RefreshDatabase` trait in Laravel.
- The example only uses one database for simplicity, but you could obviously configure a different database to copy to, if applicable.
- I'm not a big fan of unit tests. I usually write integration tests. As far as I know, I would need to make the methods public in order to be able to test them correctly with unit tests (I could be wrong here, a few years have passed since I last wrote unit tests). And I don't like compromising code structure for satisfying test requirements. I also had to do some funky stuff with the database connection (pass a new connection to each method that I test). But I still did it here to satisfy the requirement set out by you; just FYI that I think it's far from ideal :) And I included one example for a (albeit very complex) integration test.