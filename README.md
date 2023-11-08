# Stream Events

## Assignment Description

### Application

This application is aimed at showing streamers a list of events that happened during their stream.

### Registration

Users should be able to create an account through your preferred OAuth login system, this can
be anything from Twitch, Youtube, Facebook, etc..

### Assignment Requirements

- Create the following tables:
  - followers (name)
  - subscribers (name + subscription tier 1/2/3)
  - donations (amount + currency + donation message)
  - merch_sales (item name + amount + price)
- Seed each table with about 300-500 rows of data for each user with creation dates ranging from 3 months ago till now
- Each of these rows should be able to be marked as read / unread by the user
- Aggregate the data from the above three tables
  - Show it to the user once they log in
  - Use a single list to display this information, format it as a sentence
    - RandomUser1 followed you!
    - RandomUser2 (Tier1) subscribed to you!
    - RandomUser3 donated 50 USD to you! "Thank you for being awesome"
    - RandomUser4 bought some fancy pants from you for 30 USD!
  - Only show the first 100 events
  - Load more as they scroll down
- Above the list show three squares with the following information
  - Total revenue they made in the past 30 days from Donations, Subscriptions & Merch sales
      - Subscriptions are Tier1: 5$, Tier2: 10$, Tier3: 15$
  - Total amount of followers they have gained in the past 30 days
  - Top 3 items that did the best sales wise in the past 30 days


## Installation

1. Clone repo
2. Run `cd stream-events`
3. Copy `.env.example` to `.env`
4. Run `docker run --rm \
   -u "$(id -u):$(id -g)" \
   -v "$(pwd):/var/www/html" \
   -w /var/www/html \
   laravelsail/php82-composer:latest \
   composer install --ignore-platform-reqs`
5. Run `./vendor/bin/sail up -d`
6. Run `./vendor/bin/sail artisan key:generate`
7. Run `./vendor/bin/sail artisan migrate`
8. Run `./vendor/bin/sail npm install`
9. Run `./vendor/bin/sail npm run build`
10. Set GitHub OAuth app credentials in `.env`
11. Run `./vendor/bin/sail artisan queue:work`
12. Visit http://localhost

## Running tests

1. Run ` ./vendor/bin/sail artisan test`

## Comments

### Installation

I use Jobs to seed DB with events. The seeding job starts right after log in. It may take several seconds.  When you log out, starts job that clear DB.

### About authentication

I've done authentication using Laravel Sanctum package with cookie based authentication, which is a more secure and highly recommended way for SPA.

But according to this: “Be sure to use REST API calls from the frontend side to call the backend.” maybe by this, you mean stateless API which will use `access_token`/`refresh_token` or JWT for authentication purposes. But I’m not sure :) My reasoning was:

- I still have to keep tokens in cookies, because storing secure data in localStorage is not a good idea due to the XSS vulnerability.
- As I know, there aren't many good solutions for how to make authentication for SPA in a stateless manner with Socialite flow and one of the best is to use Auth JavaScript SDK and then get tokens from API. But it will increase time for development of the frontend part.
- According to this: “The main focus of this assignment is the Backend implementation” - I don’t have to spend a lot of time on frontend.

So I decided that for current project requirements Laravel Sanctum with cookie based authentication is the best choice

### About Events Retrieving

Honestly, I don't know exactly what decision you expected me to make :) But the problem with the events list looks like a perfect fit for a document oriented database such as Elastic (especially if complex aggregations will be needed in the future) or maybe MongoDB with a list of all events. This would simplify time-related operations, and such databases are production-ready and pretty fast on read and write.

But according to this: “PHP, Laravel, Vue, React, TypeScript, MySQL” I must focus on this technologies and, as I know, create query with good performance and with combination of UNION + ORDER BY + LIMIT with offset on tables with millions of rows is going to be quite challenging task considering the nature of the MySQL. It looks like not the problem of a particular query, but the problem of service design or at least DB design. So I decided to create an additional table to store all events as they occur.
I hope I understood the assignment correctly :)

### Tests

I didn't cover all the functionality with tests as I should have done on a real project. I just wrote several different types of tests to show how I usually do it on my projects.

