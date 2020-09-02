# Tranxit Request Features

* checking the staging environment
* Promocode
* Wallet
* Scheduling Ride
* ETA / Price Estimation
* Stripe Card Payment
* Surge Pricing
* Fleet Management
* User Management
* Provider Management
* Dispatcher Management
* Account Management
* Fleet Management
* Mobile OTP Verification
* Summary / Daily target
* Provider can cancel till the user is picked up.


# PHP Installation

Install php 7.1 and disable other versions

sudo apt install

sudo add-apt-repository ppa:ondrej/php

sudo apt-get update

sudo apt install php7.1

sudo apt install php7.1-cli php7.1-common php7.1-json php7.1-opcache php7.1-mysql php7.1-mbstring php7.1-mcrypt php7.1-zip composer php7.1-curl php7.1-fpm php7.1-xsl php7.1-xml php7.1-xmlrpc php7.1-gd  php7.1-gmp



sudo a2dismod {OTHER VERSION}
sudo a2enmod php7.1

sudo systemctl restart apache2

# Laravel Installation

In project folder,

composer install

cp .env.example .env 

*//Add your database credentials in env*

php artisan key:generate

php artisan storage:link

mkdir -p public/uploads

chmod -R 777 public/uploads/

php artisan migrate --seed

php artisan passport:install

chmod -R 777 storage/ bootstrap/

chown -R www-data storage/ config/

sudo chgrp -R www-data storage bootstrap/cache public

sudo chmod -R ug+rwx storage bootstrap/cache public

chmod -R 600 storage/oauth*

*//Save some data in admin settings, it will create a constants in config folder*

sudo chown -R www-data constants.php

sudo chgrp -R www-data constants.php

php artisan config:clear 

php artisan cache:clear 





