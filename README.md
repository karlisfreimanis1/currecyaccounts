<h2>Installation:</h2>
<ul>
  <li>git clone https://github.com/karlisfreimanis1/currecyaccounts.git</li>
  <li>cd currecyaccounts</li>
  <li>cp .env.example .env</li>
  <li>docker compose up --build -d</li>
  <li>docker exec -it mintos-php /bin/sh</li>
  <li>composer install</li>
  <li>chmod -R ug+rwx storage bootstrap/cache && chgrp -R www-data storage bootstrap/cache</li>
  <li>php artisan migrate</li>
  <li>php artisan db:seed [optional for dummy data]</li>
  <li>echo xdebug.mode=coverage > /usr/local/etc/php/conf.d/xdebug.ini [optinal]</li>
  <li>vendor/bin/phpunit --configuration phpunit.xml --coverage-html tests/reports [optinal]</li>
</ul>

<h2>Task: Service should expose an HTTP API providing the following functionality</h2>
<ul>
  <li>1# Given a client id return list of accounts (each client might have 0 or more accounts
with different currencies)</li>
  <li>2# Given an account id return transaction history (last transactions come first) and
support result paging using “offset” and “limit” parameters</li>
  <li>3# Transfer funds between two accounts identified by ids</li>
</ul>

<h2>Requirements</h2>
<ul>
  <li>4# Balance must always be positive (>= 0)</li>
  <li>5# Currency conversion should take place when transferring funds between accounts with
different currencies</li>
  <li>6# Use current currency exchange rates from https://api.exchangerate.host/lates</li>
  <li>7# Limit the currencies supported by your implementation based on what supported by https://api.exchangerate.host/latest</li>
  <li>8# DB schema versioning should be implemented</li>
  <li>9# Test coverage should be not less than 80%</li>
  <li>10# Implemented web service should be resilient to 3rd party service unavailability</li>
</ul>

<h2>API paths</h2>
<ul>
  <li>#1 http://localhost/api/account-list</li>
  <li>#2 http://localhost/api/account-transactions</li>
  <li>#3 http://localhost/api/transfer-fonds</li>
</ul>

<h2>Final notes</h2>
<p>I did as much as I could in the time that I had. Unfortunately isn't enough to fulfill all requirements. I use tasks like these to learn, but this time got stuck with a queue that didn't work as expected and in the documentation described. So I didn't manage to get working "service should be resilient to 3rd party service unavailability" and when I noticed that it wasn't progressing as well as expected I stopped writing tests, so it is not even close to 80% of test coverage.</p>
