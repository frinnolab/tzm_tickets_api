# Smart Support Ticketing System (Backend)

This is the backend implementation for the Smart Support Ticketing System using Laravel 10.
It provides REST API endpoints for User Registration, Authentication (via Laravel Passport), Ticket Management (CRUD), and AI suggested responses.

## Setup Instructions

1. **Install Dependencies**
   ```bash
   composer install
   ```

2. **Environment Setup**
   Copy the example `.env` file and set your database connection properly to MySQL.
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Add your database credentials in `.env`, e.g.,
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=tzm_be
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   Provide your OpenAI API Key for suggestions in `.env`:
   ```env
   OPENAI_API_KEY=your_openai_api_key_here
   ```

3. **Database and Passport Setup**
   Run the database migrations to create the required tables:
   ```bash
   php artisan migrate
   ```
   Install and generate encryption keys for Laravel Passport (to generate Personal Access tokens):
   ```bash
   php artisan passport:install
   ```
   *(Note: This creates the encryption keys and Personal Access Client for Token generation.)*

4. **Run the PHP Server**
   Start the Laravel local development server:
   ```bash
   php artisan serve
   ```
   The backend API will be available at: `http://localhost:8000/api`

## API Endpoints Overview

- `POST /api/register` : Register a new user
- `POST /api/login` : Login as a user and get a Bearer API token
- `GET /api/tickets` : View support tickets of the authenticated user
- `POST /api/tickets` : Submit a new ticket (`title`, `description`)
- `GET /api/tickets/{ticket}` : View details of a specific ticket
- `POST /api/tickets/{ticket}/suggest` : Get AI response based on ticket description
