# Local Environment Setup

## Getting Started

To spin up the development environment, follow the instructions below.

### Prerequisites

Make sure Docker and Docker Compose are installed on your machine. If not, you can install them from the official Docker website: https://www.docker.com/get-started

### Running the Application

1. Copy project to your local machine.

2. Navigate to the project directory.
```shell
cd <project-directory>
```
3. Spin up the development environment using Docker Compose.

```shell
docker compose up -d
```

4. Access the application by visiting http://localhost in your browser.

### Testing the Application

#### Postman
A postman collection export has been included in the project named  
```jared-spencer-penn-example.postman_collection.json``` 

#### PHP Unit
PHP-Unit can be run via the command

````shell
cd /var/www/html
./vendor/bin/phpunit  --bootstrap vendor/autoload.php --testdox tests
````

### Stopping the Application

To stop the development environment, run the following command:
```shell
cd <project-directory>
docker compose down
```
