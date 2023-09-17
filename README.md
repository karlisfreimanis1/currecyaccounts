<p>This is not going to be easy, more likely impossible to build scalable error-prone solution in short time. I have never worked with inner money management systems. I would probably spend a day
reviewing best practices and testing some similar open-source solutions. At least 2 days building solution and at least one more day testing. Even then there would be some shortcomings</p>

<h2>Installation:</h2>
<ul>
  <li>git clone https://github.com/karlisfreimanis1/currecyaccounts.git</li>
  <li>cd currecyaccounts</li>
  <li>docker compose up --build --force-recreate -d</li>
  <li>docker exec -it mintos-php /bin/sh</li>
  <li>chmod -R ug+rwx storage bootstrap/cache && chgrp -R www-data storage bootstrap/cache</li>
  <li></li>
  <li></li>
  <li></li>
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
