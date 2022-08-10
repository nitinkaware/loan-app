# Loan App Game
## The APP

- Frontend: No frontend
- Backend: <http://localhost:80/api>
- Database
    - Hostname: `mysql`
    - Port: `3306`
    - Username: `root`
    - Password:
    - Database: `loan_app`

### Default tech stack

- PHP 8.1.4
- Laravel Framework 9.19.0
- MySQL 8.0

### My Tech Stack

- [X] PHPStorm
- [X] MySql
- [X] Laravel
- [X] Some spatie packages

## Instructions to run the app

- Composer install
- Copy the `.env.example` to `.env` and fill the values
- php artisan key:generate && php artisan migrate:fresh --seed
- Use the postman collection to test the api

[Clone the project](https://github.com/nitinkaware/loan-app)

```bash
git clone https://github.com/nitinkaware/loan-app
```

Go to the project directory

```bash
cd loan-app
```

Install dependencies

```bash
composer install
```

Start the server with [Sail](https://laravel.com/docs/master/sail#starting-and-stopping-sail) - [(make sure you have docker installed)](https://docs.docker.com/get-docker/) OR Laravel Valet (Valet Preferences)

Run tests
```bash
php artisan test
```

Run migrations and seed the database
```bash
php artisan migrate:fresh --seed
```

### API Reference

#### Register a user

```http
POST /api/register
```

| Parameter  | Type      | Description                                              |
| :--------  | :-------  | :-------------------------                               |
| `email`    | `string`   | **Required**.                          |
| `name`    | `string`   | **Required**.                    |
| `password` | `string` | Required and must match with password_confirmation |
| `password_confirmation` | `string` | Required  |

#### Login
This api is same for both users and admins.

```http
POST /api/login
```

| Parameter | Type     | Description   |
| :-------- | :------- | :------------ |
| `email`  | `string` | **Required** |
| `password`  | `string` | **Required** |
| `device_name`  | `string` | **Required** |

#### Request a new loan [AUTH REQUIRED]

```http
POST /api/loan-requests
```
| Parameter | Type     | Description   |
| :-------- | :------- | :------------ |
| `amount_required`  | `float` | **Required** |
| `terms_in_week`  | `integer` | **Required** |

#### Approve a loan [ADMIN AUTH REQUIRED]
No request payload required.

```http
POST /api/loan-requests/approve/{loan}
```

#### Make a repayment [AUTH REQUIRED]

```http
POST /api/loan-repayments/repayment/{repayment}
```
| Parameter | Type     | Description   |
| :-------- | :------- | :------------ |
| `amount_paid`  | `float` | **Required** |

#### View all my all loans [AUTH REQUIRED]

```http
GET /api/loans
```
| Parameter | Type     | Description   |
| :-------- | :------- | :------------ |
| `cursor`  | `string` | **Optional** |
