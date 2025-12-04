# Todo List API - Talenavi

REST API untuk manajemen todo list dengan fitur export Excel dan tracking waktu.

## 📋 Fitur Utama

-   ✅ **CRUD Todo**: Create, Read, Update, Delete todo items
-   📊 **Export Excel**: Export todo list dengan filter yang dapat disesuaikan
-   ⏱️ **Time Tracking**: Lacak waktu yang dihabiskan untuk setiap todo
-   🔍 **Advanced Filtering**: Filter berdasarkan:
    -   Title (partial match)
    -   Assignee (multiple)
    -   Due Date (range)
    -   Time Tracked (range)
    -   Status (pending, open, in_progress, completed)
    -   Priority (low, medium, high)
-   📈 **Summary Statistics**: Total todos dan total time tracked dalam export

## 🛠️ Tech Stack

-   **Framework**: Laravel 12
-   **Database**: MySQL
-   **Export**: Maatwebsite Excel
-   **ORM**: Eloquent
-   **Build Tool**: Vite

## 📦 Installation

### Prerequisites

-   PHP 8.2+
-   Composer
-   Node.js & npm

### Setup

1. **Clone repository**

```bash
git clone git@github.com:misbahkun/todos-talenavi.git
cd todos-talenavi
```

2. **Install PHP dependencies**

```bash
composer install
```

3. **Install Node dependencies**

```bash
npm install
```

4. **Setup environment**

```bash
cp .env.example .env
php artisan key:generate
```

5. **Run migrations**

```bash
php artisan migrate
```

6. **Start development server**

```bash
composer run dev
```

Server akan berjalan di `http://localhost:8000`

## 📚 API Endpoints

### Create Todo

```
POST /api/v1/todos
```

**Request Body:**

```json
{
    "title": "Implement Todo API",
    "assignee": "Misbahudin",
    "due_date": "2025-12-31",
    "time_tracked": 0,
    "status": "pending",
    "priority": "high"
}
```

**Response:** 201 Created

```json
{
    "message": "Todo created successfully",
    "data": {
        "id": 1,
        "title": "Implement Todo API",
        "assignee": "Misbahudin",
        "due_date": "2025-12-31",
        "time_tracked": "0",
        "status": "pending",
        "priority": "high",
        "created_at": "2025-12-05T10:30:00Z",
        "updated_at": "2025-12-05T10:30:00Z"
    }
}
```

### Export Todos to Excel

```
GET /api/v1/todos/export
```

**Query Parameters (optional):**

-   `title`: Partial match (e.g., `?title=Implement`)
-   `assignee`: Multiple comma-separated (e.g., `?assignee=John,Doe`)
-   `start`: Due date start (e.g., `?start=2025-01-01`)
-   `end`: Due date end (e.g., `?end=2025-12-31`)
-   `min`: Min time tracked (e.g., `?min=0`)
-   `max`: Max time tracked (e.g., `?max=100`)
-   `status`: Multiple comma-separated (e.g., `?status=pending,in_progress`)
-   `priority`: Multiple comma-separated (e.g., `?priority=high,medium`)

**Response:** 200 OK

```json
{
    "message": "Excel generated and stored successfully",
    "file_name": "todos_20251205_103000.xlsx",
    "file_url": "http://localhost:8000/storage/exports/todos_20251205_103000.xlsx"
}
```

**Excel Output:**

-   Columns: Title, Assignee, Due Date, Time Tracked, Status, Priority
-   Summary rows at bottom:
    -   TOTAL
    -   todos: {count}
    -   time tracked: {sum}

## 🗄️ Database Schema

### Todos Table

```sql
CREATE TABLE todos (
  id BIGINT PRIMARY KEY,
  title VARCHAR(255),
  assignee VARCHAR(255) NULLABLE,
  due_date DATE,
  time_tracked INTEGER DEFAULT 0,
  status ENUM('pending', 'open', 'in_progress', 'completed') DEFAULT 'pending',
  priority ENUM('low', 'medium', 'high'),
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

## 📂 Project Structure

```
├── app/
│   ├── Exports/
│   │   └── TodosExport.php          # Excel export class
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── TodoController.php   # Todo API controller
│   │   └── Requests/
│   │       └── StoreTodoRequest.php # Request validation
│   └── Models/
│       └── Todo.php                 # Todo model
├── database/
│   ├── migrations/
│   │   └── 2025_12_04_213343_create_todos_table.php
│   └── seeders/
├── routes/
│   ├── api.php                      # API routes
│   └── web.php
├── storage/
│   └── app/
│       └── public/
│           └── exports/             # Exported Excel files
└── composer.json
```

## 📝 Notes

-   Default nilai `time_tracked` adalah 0 jika tidak dikirim
-   Default nilai `status` adalah `pending` jika tidak dikirim
-   Export file disimpan di `storage/app/public/exports/`
-   File dapat diakses via browser di `/storage/exports/{filename}`



## 👤 Author

Misbahudin - Talenavi Project
