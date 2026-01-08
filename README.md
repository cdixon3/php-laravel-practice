# Laravel REST API - Task Manager

A basic REST API built with Laravel 12 and PostgreSQL to demonstrate core Laravel concepts and API development skills. This project showcases fundamental CRUD operations, database relationships, and modern PHP development practices.

## Features

- RESTful API endpoints for task management
- PostgreSQL database integration
- Docker Compose for easy local development
- Input validation and error handling
- Resource-based routing
- JSON API responses
- Comprehensive test coverage with PHPUnit

## Tech Stack

- **PHP 8.4**
- **Laravel 12**
- **PostgreSQL 16**
- **Docker & Docker Compose**

## Prerequisites

Make sure you have the following installed on your system:
- [Docker Desktop](https://www.docker.com/products/docker-desktop/)
- [Git](https://git-scm.com/)

## Getting Started

### 1. Clone the Repository

```bash
git clone <repository-url>
cd php-laravel-practice
```

### 2. Start the Application

```bash
docker-compose up -d --build
```

This will:
- Build the Laravel application container
- Start a PostgreSQL database container
- Install PHP dependencies
- Run database migrations
- Start the Laravel development server

### 3. Verify the Application is Running

Once the containers are up, you can test the API health check:

```bash
curl http://localhost:8000/api/health
```

Expected response:
```json
{
  "status": "ok",
  "message": "API is running",
  "timestamp": "2025-01-07T19:30:00Z"
}
```

## API Endpoints

All endpoints are prefixed with `/api`

### Tasks Resource

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/tasks` | Get all tasks |
| POST | `/api/tasks` | Create a new task |
| GET | `/api/tasks/{id}` | Get a specific task |
| PUT/PATCH | `/api/tasks/{id}` | Update a task |
| DELETE | `/api/tasks/{id}` | Delete a task |

### Health Check

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/health` | API health check |

## API Usage Examples

### Create a Task

```bash
curl -X POST http://localhost:8000/api/tasks \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Learn Laravel",
    "description": "Study Laravel fundamentals and build REST APIs",
    "completed": false
  }'
```

Response:
```json
{
  "success": true,
  "message": "Task created successfully",
  "data": {
    "id": 1,
    "title": "Learn Laravel",
    "description": "Study Laravel fundamentals and build REST APIs",
    "completed": false,
    "created_at": "2025-01-07T19:30:00.000000Z",
    "updated_at": "2025-01-07T19:30:00.000000Z"
  }
}
```

### Get All Tasks

```bash
curl http://localhost:8000/api/tasks
```

Response:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Learn Laravel",
      "description": "Study Laravel fundamentals and build REST APIs",
      "completed": false,
      "created_at": "2025-01-07T19:30:00.000000Z",
      "updated_at": "2025-01-07T19:30:00.000000Z"
    }
  ]
}
```

### Get a Specific Task

```bash
curl http://localhost:8000/api/tasks/1
```

### Update a Task

```bash
curl -X PUT http://localhost:8000/api/tasks/1 \
  -H "Content-Type: application/json" \
  -d '{
    "completed": true
  }'
```

Response:
```json
{
  "success": true,
  "message": "Task updated successfully",
  "data": {
    "id": 1,
    "title": "Learn Laravel",
    "description": "Study Laravel fundamentals and build REST APIs",
    "completed": true,
    "created_at": "2025-01-07T19:30:00.000000Z",
    "updated_at": "2025-01-07T19:35:00.000000Z"
  }
}
```

### Delete a Task

```bash
curl -X DELETE http://localhost:8000/api/tasks/1
```

Response:
```json
{
  "success": true,
  "message": "Task deleted successfully"
}
```

## Project Structure

```
.
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Api/
│   │           └── TaskController.php    # API controller
│   └── Models/
│       └── Task.php                      # Task model
├── database/
│   ├── factories/
│   │   └── TaskFactory.php               # Task factory for testing
│   └── migrations/
│       └── 2025_01_07_000001_create_tasks_table.php
├── routes/
│   └── api.php                           # API routes
├── tests/
│   └── Feature/
│       └── TaskApiTest.php               # API endpoint tests
├── docker-compose.yml                    # Docker services configuration
├── Dockerfile                            # PHP/Laravel container
└── .env                                  # Environment variables
```

## Development

### View Logs

```bash
# Application logs
docker-compose logs -f app

# Database logs
docker-compose logs -f db
```

### Access the Application Container

```bash
docker-compose exec app bash
```

### Run Artisan Commands

```bash
docker-compose exec app php artisan <command>
```

### Stop the Application

```bash
docker-compose down
```

### Stop and Remove Volumes (Database Data)

```bash
docker-compose down -v
```

## Testing

This project includes comprehensive feature tests for all API endpoints. The tests use an in-memory SQLite database for fast execution.

### Run All Tests

```bash
docker-compose exec app php artisan test
```

### Run Tests with Coverage

```bash
docker-compose exec app php artisan test --coverage
```

### Run Specific Test File

```bash
docker-compose exec app php artisan test --filter=TaskApiTest
```

### Test Coverage

The test suite includes **17 tests** covering:

- **Health Check**: API health endpoint validation
- **CRUD Operations**:
  - List all tasks (empty and populated)
  - Create new tasks
  - Retrieve specific tasks
  - Update tasks (full and partial updates)
  - Delete tasks
- **Validation**:
  - Required field validation
  - Field length validation
  - Data type validation
- **Error Handling**:
  - 404 responses for non-existent resources
  - 422 responses for validation errors
- **Business Logic**:
  - Tasks ordered by creation date (descending)
  - Boolean field handling

### Test Example Output

```
PASS  Tests\Feature\TaskApiTest
✓ health endpoint returns success
✓ can get empty task list
✓ can get all tasks
✓ can create task
✓ cannot create task without title
✓ cannot create task with too long title
✓ can get specific task
✓ cannot get nonexistent task
✓ can update task
✓ can partially update task
✓ cannot update nonexistent task
✓ can delete task
✓ cannot delete nonexistent task
✓ tasks are ordered by created at desc
✓ can create task with boolean completed

Tests:  17 passed (70 assertions)
```

## Environment Variables

Key environment variables in `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=laravel
DB_USERNAME=laravel
DB_PASSWORD=secret
```

## Testing with Postman or Insomnia

You can import the API endpoints into Postman or Insomnia for easier testing:

1. Base URL: `http://localhost:8000/api`
2. Set headers: `Content-Type: application/json`
3. Use the endpoint examples above

## Learning Resources

This project demonstrates the following Laravel concepts:

- **Routing**: API resource routing with `Route::apiResource()`
- **Controllers**: RESTful controller structure
- **Models**: Eloquent ORM models with mass assignment protection
- **Migrations**: Database schema management
- **Validation**: Request validation with rules
- **JSON Responses**: Structured API responses
- **Database**: PostgreSQL integration with Eloquent
- **Testing**: Feature tests with PHPUnit, factories, and RefreshDatabase trait

## Future Enhancements

Potential improvements to explore:

- Authentication with Laravel Sanctum
- API rate limiting
- Request/Response transformation with API Resources
- API documentation with Swagger/OpenAPI
- Pagination for large datasets
- Filtering and sorting capabilities
- CI/CD pipeline with GitHub Actions

## License

This is a learning project created for demonstration purposes.
