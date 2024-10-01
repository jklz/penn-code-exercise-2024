# Local Environment Setup

## Getting Started

To spin up the development environment, follow the instructions below.

### Prerequisites

Make sure Docker and Docker Compose are installed on your machine. If not, you can install them from the official Docker website: https://www.docker.com/get-started

### Running the Application

1. Copy project to your local machine.

2. Navigate to the project directory.
```bash
cd <project-directory>
```
3. Spin up the development environment using Docker Compose.

```bash
docker-compose up -d
```

4. Access the application by visiting http://localhost in your browser.

### Stopping the Environment

To stop the development environment, run the following command:
```bash
docker-compose down
```