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
symfony-console doctrine:migrations:migrate
```

### Api docs
```
http://127.0.0.1:3003/api/doc
```

### Tasks

- MD-1 Init project
- MD-2 Create database structure
- MD-3 Create draft for api -> 
- MD-4 Create draft for ShowPaymentsByDate -> symfony-console ShowPaymentsByDate report --date=2025-07-21
- MD-5 Clean up Payment controller introduce basic level abstraction
- MD-6 Finish basic Payment import 
- MD-7 Create command for csv import -> cd src/Resources/files && symfony-console ImportPaymentFromCsv import --file=payments.csv
- MD-8 Create PaymentAssignService
- MD-9 Create Refund
- MD-10 Configure and add logs -> /var/log/payment.log
- MD-11 Generate/write some test -> php vendor/bin/phpunit --testdox
- MD-12 Remove irrelevant template files
- MD-13 Format README

#### It is the draft not solution. Not enough time for

- Communication functionality implementation
- set indexes on relevant selects
- cleanup for db, double check proper field types, same with entities, crypt sensitive fields
- payment processing and payment orders as events
- scalability

#### In the end I doubt payment/refund logic should ever be 1 day task, especially with limited clarity in requirements 


