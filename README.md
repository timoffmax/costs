# Service for managing your costs

This service written with using Laravel and VueJS technologies allows you to track your costs and incomes. You'll be able to watch statistics of all the operations, find the needed one, separate operations by different accounts and plan your future costs.

***Warning:*** it's still under development! Most of the functionality hasn't been done yet.

## Installation of the project
### Standard way
- `composer install`
- `npm i`
- `cp .env.example .env`
- Create a database and specify its name inside `.env` config file
- `./artisan key:generate`
- `./artisan migrate`
- `./artisan passport:keys`
- `npm run dev`

### Docker way
- `cp .env.example .env`
- Adjust database connection variables inside the file
  - `DB_HOST=costs-db`
  - `DB_PORT=3306`
  - `DB_DATABASE=costs`
  - `DB_USERNAME=root`
  - `DB_PASSWORD=root`
- Build and run the containers
  - `docker-compose up -d`
- Login into the main container
  - `docker-compose exec costs-app /bin/bash`
- Install the app dependencies and so on
  - `composer install`
  - `npm i`
  - `./artisan key:generate`
  - `./artisan migrate`
  - `./artisan passport:keys`
  - `npm run dev`

## Default admin account
- *email*: admin@costs.local
- *password:* admin

## Used integrations
### Course convert service
Free version of https://www.currencyconverterapi.com/ service is used. In case you're going to use different currencies, you must register a free account and fill the `CURR_CONV_API_KEY` ENV variable. Then you need to set up Laravel cron.
