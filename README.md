# Abicart Assignment 2
## Subscribe to movies and keep its track.

This code is made for the assignment from Abicart. It is made on top of Laravel 8 and in PHP.

## Features

- Subscribe to movies from https://star-wars-api.herokuapp.com/films . Configurable and extendable
- Unsubscribe
- Track Subscriptions

> NOTE: Authentication System is not made as it was not required. You can pass user_id manually through POST parameters.


## Database Design

Database file can be seen from migrations (thanks to laravel). Also PDF has been mailed.

## Installation

This application requires PHP to run.

Install the dependencies from composer and start the server

```sh
composer install
php artisan key:generate  
```

Copy .example.env to .env and configure database as per requirement. Then run

```sh
php artisan migrate --seed
```
Some of the rows have already been inserted for demo to start working.



## API End Points

1. POST api/abicart/subscribe . Input: {"user_id":"1","provider_id":"1","provider_identifier":"5"} , Output: {"status":1,"expiry":"2021-08-28 20:34:57","message":"Subscription Success"}
2. POST api/abicart/unsubscible - Input: {"user_id":"1","provider_id":"1","provider_identifier":"5"} , Output {"status":1,"message":"Un Subscribe Success"}
3. GET api/abicart/getActiveSubscriptions - Input {?user_id=1} , Output {"status":1,"movies":[{"provider":1,"provider_identifier":"3","title":"Episode VI - Return of the Jedi","release_date":"1983-05-25"},{"provider":1,"provider_identifier":"4","title":"Episode 1 - The Phantom Menace","release_date":"1999-05-19"}]}