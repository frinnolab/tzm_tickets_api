# Smart Support Ticketing System - Backend (Laravel)

This is the backend API for the Smart Support Ticketing System, built with **Laravel 10**. It handles user authentication, ticket lifecycle management, and integrates with **Google Gemini AI** to provide smart suggestions for support resolutions.

## 🚀 Getting Started

### Prerequisites
- PHP >= 8.2
- Composer
- MySQL/MariaDB
- A Google Gemini API Key (from [Google AI Studio](https://aistudio.google.com/))

### Installation Steps

1. **Clone and Install Dependencies**
   ```bash
   composer install
   ```

2. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   Open your `.env` file and configure your database and Gemini key:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=tzm_be
   DB_USERNAME=root
   DB_PASSWORD=

   GEMINI_API_KEY=your_gemini_api_key_here
   ```

3. **Database Migration & Auth Setup**
   The project uses **Laravel Passport** for secure API authentication.
   ```bash
   php artisan migrate
   php artisan passport:install
   ```
   *Note: `passport:install` will generate the encryption keys and personal access clients required for token generation.*

4. **Start the Server**
   To ensure compatibility with the pre-configured mobile app, run the server on port **8001**:
   ```bash
   php artisan serve --port=8001
   ```
   The API will be accessible at: `http://127.0.0.1:8001/api`

## 🛠️ Key Features
- **User Authentication**: Secure Register/Login flow using OAuth2 Personal Access Tokens.
- **Ticket Management**: Full CRUD for support tickets specialized for the logged-in user.
- **AI Integration**: Integration with `gemini-1.5-flash-lite` to analyze ticket descriptions and suggest resolutions.
- **RESTful Design**: Standardized JSON responses with appropriate HTTP status codes.

## 📡 API Endpoints
- `POST /api/register` - Create a new account.
- `POST /api/login` - Authenticate and receive a Bearer token.
- `GET /api/tickets` - List user's tickets.
- `POST /api/tickets` - Create a new ticket.
- `GET /api/tickets/{id}` - View specific ticket details.
- `PATCH /api/tickets/{id}/close` - Mark a ticket as resolved.
- `POST /api/tickets/{id}/suggest` - Generate AI resolution suggestion.
