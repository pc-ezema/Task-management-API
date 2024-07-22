# Task Management System

## Overview

This is a Task Management System built with Laravel. It provides APIs for managing tasks and user authentication.

## Authentication

### Register

- **Method**: POST
- **URL**: `/api/register`
- **Request Body**:
    ```json
    {
        "name": "User Name",
        "email": "user@example.com",
        "password": "password"
    }
    ```
- **Response**:
    - **Success**: 201 Created
    - **Error**: 422 Unprocessable Entity

### Login

- **Method**: POST
- **URL**: `/api/login`
- **Request Body**:
    ```json
    {
        "email": "user@example.com",
        "password": "password"
    }
    ```
- **Response**:
    - **Success**: 200 OK
    - **Error**: 401 Unauthorized

- **Response Body**:
    ```json
    {
        "token": "your_access_token",
        "user": {
            "id": 1,
            "name": "User Name",
            "email": "user@example.com"
        }
    }
    ```

### Forget Password

- **Method**: POST
- **URL**: `/api/forgot-password`
- **Request Body**:
    ```json
    {
        "email": "user@example.com"
    }
    ```
- **Response**:
    - **Success**: 200 OK
    - **Error**: 404 Not Found, 422 Unprocessable Entity

### Reset Password

- **Method**: POST
- **URL**: `/api/reset-password`
- **Request Body**:
    ```json
    {
        "code": "reset_code",
        "email": "user@example.com",
        "password": "new_password",
        "password_confirmation": "new_password"
    }
    ```
- **Response**:
    - **Success**: 200 OK
    - **Error**: 422 Unprocessable Entity

### Logout

- **Method**: POST
- **URL**: `/api/logout`
- **Headers**:
    - **Authorization**: Bearer `your_access_token`
- **Response**:
    - **Success**: 200 OK
    - **Error**: 401 Unauthorized

## Task Management

### Create Task

- **Method**: POST
- **URL**: `/api/tasks`
- **Request Body**:
    ```json
    {
        "title": "Sample Task",
        "description": "Task Description",
        "completed": false
    }
    ```
- **Response**:
    - **Success**: 201 Created
    - **Error**: 401 Unauthorized, 422 Unprocessable Entity

### Read Tasks

- **Method**: GET
- **URL**: `/api/tasks`
- **Response**:
    - **Success**: 200 OK
    - **Error**: 401 Unauthorized

### Read Task by ID

- **Method**: GET
- **URL**: `/api/tasks/{id}`
- **Response**:
    - **Success**: 200 OK
    - **Error**: 404 Not Found, 401 Unauthorized

### Update Task

- **Method**: PUT
- **URL**: `/api/tasks/{id}`
- **Request Body**:
    ```json
    {
        "title": "Updated Task",
        "description": "Updated Description",
        "completed": true
    }
    ```
- **Response**:
    - **Success**: 200 OK
    - **Error**: 404 Not Found, 401 Unauthorized

### Delete Task

- **Method**: DELETE
- **URL**: `/api/tasks/{id}`
- **Response**:
    - **Success**: 200 OK
    - **Error**: 404 Not Found, 401 Unauthorized

### Complete Task

- **Method**: PUT
- **URL**: `/api/complete-task/{id}`
- **Request Body**:
    ```json
    {
        "completed": true
    }
    ```
- **Response**:
    - **Success**: 200 OK
    - **Error**: 404 Not Found, 401 Unauthorized

## Postman Collection

You can view and test the API using the Postman collection [here](your-postman-collection-url).

## Setup

1. **Clone the repository**:
    ```bash
    git clone https://github.com/your-repo/task-management-system.git
    cd task-management-system
    ```

2. **Install dependencies**:
    ```bash
    composer install
    ```

3. **Set up the environment**:
    - Copy `.env.example` to `.env` and configure your database and other settings.
    - Run database migrations:
      ```bash
      php artisan migrate
      ```

4. **Run the application**:
    ```bash
    php artisan serve
    ```

5. **Run tests**:
    ```bash
    php artisan test
    ```

## Contributing

Feel free to submit issues, pull requests, and suggestions. Contributions are welcome!

