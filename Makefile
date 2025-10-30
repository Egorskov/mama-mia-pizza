
test:
	docker exec -it mama-mia-pizza-laravel.test-1 php artisan test

test-good:
	docker exec -it mama-mia-pizza-laravel.test-1 php artisan test --filter=GoodControllerTest

test-order:
	docker exec -it mama-mia-pizza-laravel.test-1 php artisan test --filter=OrderControllerTest

test-admin:
	docker exec -it mama-mia-pizza-laravel.test-1 php artisan test --filter=AdminControllerTest
