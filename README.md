# Audioteka Task

Create a shopping cart application with an API (HTTP).

*Guidelines*
* no frontend required, just expose every action through an API
* code should be written in PHP
* you can over-engineer it, we want to see your programming skills
* do not use api platform
* use docker
* remember to add tests

## env options (.env.local)

    DATABASE_URL
    API_ITEMS_PER_PAGE

## Install

    composer install
    php bin/console doctrine:database:create
    php bin/console doctrine:schema:update --force
    php bin/console doctrine:fixtures:load --append --group=users
    php bin/console doctrine:fixtures:load --append --group=products

## Create API user

    php bin/console app:create-api-user username api_key

## Show API Token

    php bin/console app:show-api-user-token username

## Start server

    sh start_server.sh

## Api Methods

`Authorization: ***` // from command app:show-api-user-token username

`Content-Type: application/json`

```
 --------------------------- -------- -------- ------ ----------------------------------- 
Name                        Method   Scheme   Host   Path
 --------------------------- -------- -------- ------ -----------------------------------  
app_api_index               GET      ANY      ANY    /                              
app_api_products_index      GET      ANY      ANY    /products?page=(int=1)&limit=(int=env(API_ITEMS_PER_PAGE))              
app_api_products_show       GET      ANY      ANY    /products/{id}                     
app_api_products_create     POST     ANY      ANY    /products/create                   
app_api_products_update     PUT      ANY      ANY    /products/update/{id}              
app_api_products_delete     DELETE   ANY      ANY    /products/delete/{id}              
app_api_cart_index          GET      ANY      ANY    /cart
app_api_cart_create         POST     ANY      ANY    /cart/create                       
app_api_cart_show           GET      ANY      ANY    /cart/{id}                         
app_api_cart_add_items      POST     ANY      ANY    /cart/add-items/{id}               
app_api_cart_remove_items   DELETE   ANY      ANY    /cart/remove-items/{id}
 --------------------------- -------- -------- ------ ----------------------------------- 
```

### app_api_products_create

Body content:

```json
{
    "name": "Product Name",
    "price": 19.99
}
```

### app_api_products_update

Body content:

```json
{
    "price": 99.99
}
```

or

```json
{
    "name": "New Product Name"
}
```

or both


```json
{
    "name": "New Product Name",
    "price": 99.99
}
```

### app_api_cart_add_items

Body content (multiple or single):

```json
[
    {
        "productId": "484f087f-c5ad-43ce-aad5-50ab4da83d8b",
        "quantity": 3
    },
    {
        "productId": "8bdd5fde-6a73-4c30-abb3-8b9a978b2cf1",
        "quantity": 1
    }
]
```

### app_api_cart_remove_items

Body content (multiple or single):

```json
[
    {
        "cartItemId": "7f396053-052b-4f44-a04a-4f5a6f9ef2c1",
        "decreaseQuantity": 3
    },
    {
        "cartItemId": "ea05d018-017b-48e9-9df8-12f695ac3d0c",
        "decreaseQuantity": 1
    }
]
```

## Docker

* https://github.com/ger86/symfony-docker


1. Create the file `./.docker/.env.nginx.local` using `./.docker/.env.nginx` as template. The value of the variable `NGINX_BACKEND_DOMAIN` is the `server_name` used in NGINX.

2. Go inside folder `./docker` and run `docker-sync-stack start` to start containers.

3. You should work inside the `php` container. This project is configured to work with [Remote Container](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers) extension for Visual Studio Code, so you could run `Reopen in container` command after open the project.

4. Inside the `php` container, run `composer install` to install dependencies from `/var/www/symfony` folder.

5. Use the following value for the DATABASE_URL environment variable:

```
DATABASE_URL=mysql://app_user:helloworld@db:3306/app_db?serverVersion=5.7
```

You could change the name, user and password of the database in the `env` file at the root of the project.

## Copyrights

Copyright (c) Rafał Mikołajun 2021.
