This is not going to be easy. I have never worked with inner money management systems. I would probably spend a day
reviewing best practices and testing some similar open-source solutions.

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
