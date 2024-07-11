# BILEMO

## Table of Contents

1. [Description](#description)
2. [Documentation](#documentation)
2. [Main Features and Endpoints](#main-features-and-endpoints)
3. [Technologies Used](#technologies-used)
4. [Libraries](#libraries)
5. [Installation](#installation)
6. [Configuration](#configuration)
7. [Contributing](#contributing)
8. [Licence](#licence)
8. [FAQ](#faq)

## Description

BileMo is a project to develop an API addressed to companies to facilitate the sale of cell phones. This API will enable users to make requests on the `"phones" and "customers" resources`. This API respects the `Richardson Maturity Model` (The swamp of POX, Ressources, HTTP Verbs, Hypermedia Controls). In addition, the project enables caching to improve performance and use JWT for security.

## Documentation

you can consult the documentation at the following address: `{{base_url}}/api/doc`

## Main Features and Endpoints
- **Get Phones**: User can consult the list of BileMo products:
  - `GET {{base_url}}/api/phones`
- **Get a Phone**: User can consult the details of a BileMo product:
  - `GET {{base_url}}/api/products/{id}`
- **Get Customers**: User can consult the list of his own registered customers:
  - `GET {{base_url}}/api/customers`
- **Get a Customer**: User can consult details of a registered user linked to a customer:
  - `GET {{base_url}}/api/customers/{id}`
- **Post a new Customer**: User can create a new customer for his pool of customers:
  - `POST {{base_url}}/api/customers`
- **Delete Customer**: User can delete a customer from his pool of customers:
  - `DELETE {{base_url}}/api/customers/{id}`
- **Security Features**:  Authentication with JWT.

## Technologies Used

- PHP 8.3.6
- Symfony 7.0
- MySQL (Relational Database)

## Libraries

- `cache`: Caching library to improve performance.
- `doctrine/orm`: Database ORM.
- `Faker`: Library for generating fake data for testing.
- `Hateoas`: A framework to support the creation of RESTful APIs following the HATEOAS (Hypermedia As The Engine Of Application State) principle.
- `JMSerializer`: Serialization and deserialization library for PHP objects.
- `lexit`: Incorrect or placeholder text.
- `Nelmio`: Tools for API documentation and other features.
- `symfony/security-bundle`: Security and user management.
- `twig/twig`: Templating engine.

## Installation

```bash
# Clone the repository
git clone https://github.com/KenKaneki-42/BileMo.git

# Change directory to the cloned repository
cd BileMo

# Install dependencies with Composer
composer install

# Create database
php bin/console doctrine:database:create

# Run database migrations
php bin/console doctrine:migrations:migrate

# Load fixtures into the database
php bin/console doctrine:fixtures:load

# Start the Symfony server
symfony server:start
```

## Configuration

### Database Configuration:
- **Copy `.env` to `.env.local`.**
  This ensures that your local settings do not interfere with the production settings.
- **Set your database URL in `.env.local` under `DATABASE_URL`.**
  Example for a MySQL database:
  ```plaintext
  DATABASE_URL="mysql://username:password@localhost:3306/database_name"

### JWT Configuration:
- Use https://symfony.com/bundles/LexikJWTAuthenticationBundle/current/index.html
- You can adjust TTL of token in lexik_jwt_authentication.yaml

## Contributing

If you want to contribute to BileMo, please follow these steps:

1. Fork the repository
2. Create a new branch (`git checkout -b feature/new-feature`)
3. Commit your changes (`git commit -am 'Add a new feature'`)
4. Push the branch (`git push origin feature/new-feature`)
5. Create a pull request

## Licence

This project is licensed under the MIT license. Please see the `LICENSE` file for more information.

## FAQ

### Q: As a User how can i be authenticated?

A: the JWT is valid for 3600 seconds by default. You can ask for a JWT. You can use cURL or a service as Postman or Insomnia

  - **curl** method: in your terminal
```bash
curl -X POST https://example.com/api/login_check \
     -H "Content-Type: application/json" \
     -H "Authorization: Bearer YOUR_JWT_TOKEN_HERE" \
     -d '{
          "username": "John",
          "password": "password"
         }'
```
  - **Postman** method:
1. Create a new request.
2. Choose method HTTP (`POST`).
3. Enter the URL and the request body (`https://example.com/api/login_check`).
4. Under the **Body** tab, select **raw** and **JSON** and add your JSON data:
   ```json
   {
     "username": "John",
     "password": "password"
   }
6. Send Request and view response and copy the JWT.

### Q: As a User, how can I create a new customer?

A: To create a new Customer, log in as a user, then copy JWT. You can use cURL or a service as Postman or Insomnia

  - **curl** method: in your terminal
```bash
curl -X POST https://example.com/api/customers \
     -H "Content-Type: application/json" \
     -H "Authorization: Bearer YOUR_JWT_TOKEN_HERE" \
     -d '{
           "firstName": "John",
           "lastName": "Doe",
           "email": "user123@example.com"
         }'
```
  - **Postman** method:
1. Create a new request.
2. Choose method HTTP (`POST`).
3. Enter the URL and the request body (`https://example.com/api/customers`).
4. Add the `Authorization` header with the Bearer token.
5. Under the **Body** tab, select **raw** and **JSON** and add your JSON data:
   ```json
   {
     "firstName": "John",
     "lastName": "Doe",
     "email": "user123@example.com"
   }
6. Send Request and view response.
