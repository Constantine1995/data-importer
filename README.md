<p align="center"><a href="#" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## Тестовый импорт данных из API в Базу данных на фреймворке Laravel

#### Реализован импорт данных:
- Продажи
- Заказы
- Склады
- Доходы

## Стек
- `php 8.2`
- `Laravel 12`

## Установка
1. Склонировать репозиторий: `git clone https://github.com/Constantine1995/data-importer.git`
2. Установить зависимости: `composer install`
3. Скопировать `.env.example` в `.env`: `cp .env.example .env`
4. Добавить ваш `API_KEY` в `.env` 
5. Настроить `.env` с данными БД (представлены ниже)
6. Сгенерировать ключ: `php artisan key:generate`
7. Запустить синхронизацию: `php artisan api:sync --date-from=2025-04-10 --date-to=`

## Доступ к БД
- Хост: `sheriff958.beget.tech`
- Порт: `3306`
- Имя БД: `sheriff958_api`
- Пользователь: `sheriff958_api`
- Пароль: `6H5hAi%0XzPW`

## Таблицы
- `incomes`
- `orders`
- `sales`
- `stocks`

## Классы
- `SyncApiData` – Команда Laravel для синхронизации данных с API. Выполняет запуск синхронизации данных за указанный период **(--date-from, --date-to)**
- `ApiService` – Сервис для работы с API. Отвечает за выполнение HTTP-запросов к внешнему API с помощью **GuzzleHttp**
- `IncomesSyncService` – Сервис для синхронизации данных о доходах с API
- `OrdersSyncService` – Сервис для синхронизации данных о заказах с API
- `SalesSyncService` – Сервис для синхронизации данных о продажах с API
- `StocksSyncService` – Сервис для синхронизации данных о складах с API
- `LogSyncService` – Абстрактный базовый класс для логирования операций синхронизации.

## Модели
- `Income`
- `Order`
- `Sale`
- `Stock`