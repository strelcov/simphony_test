Book library (demo)
============

0. Make composer install
1. Fill parameters in `app/config/parameters.yml` for databases, redis, test user auth data, apikey, domain name
1. Create database: `php bin/console doctrine:database:create`
2. Load migrations by command: `php bin/console doctrine:migrations:migrate`
3. Load fixtures by command: `php bin/console doctrine:fixtures:load` (test user and records for table `author` will be create automatically)
