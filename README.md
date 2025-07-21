# Template setup

### env
```
cp -a .docker/.env.dist .docker/.env
```
```
vim .docker/.env
```

### Installation
```
cd .docker/
```
```
docker compose up --build -d
```
```
docker exec -it md202507_payment_php /bin/sh
```
```
composer install
```
```
php bin/console doctrine:migrations:migrate
```

### Api docs
```
http://127.0.0.1:3003/api/doc
```

### Tests
phpunit.xml.dist
```
php vendor/bin/phpunit --coverage-html coverage
```

### Console
```
symfony-console
```

### Queue
```
symfony-console messenger:consume -vv
```

### Tasks

MD-1 Init project
MD-2 Create database structure
MD-3 Create draft for api
MD-4 Create draft for ShowPaymentsByDate console command
MD-5 Clean up Payment controller introduce basic level abstraction
MD-6 Finish basic Payment import 
MD-7 Create command for csv import
MD-8 Create PaymentAssignService
MD-9 Create Refund


