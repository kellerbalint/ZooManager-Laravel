# ZooManager Laravel Project

**ZooManager** is a Laravel-based web application developed for managing animals and enclosures in a zoo. The application provides functionality for creating, editing, and archiving animal and enclosure data, with user authentication and role-based access.

## 🔐 Admin Access

To access all features, use the following admin credentials:

- **Email:** `admin@a.hu`  
- **Password:** `q`

## 🚀 Getting Started

### Prerequisites

- PHP >= 8.1
- Composer
- MySQL or other supported database
- Node.js and npm (for front-end assets)

### Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/ZooManager-Laravel.git
   cd ZooManager-Laravel
Install dependencies:

```bash
composer install
npm install && npm run dev
```
Copy .env and configure your environment:

```bash
cp .env.example .env
php artisan key:generate
```
Set up your database credentials in .env, then run:

```bash
php artisan migrate --seed
```
Start the server:

```bash
php artisan serve
```
📌 Available Routes
Public Routes
/main – Home page

/login – User login

/register – User registration

Authenticated Routes
/enclosures – List all enclosures

/getEnclosure/{id} – View enclosure details

/editEnclosure/{id} – Edit enclosure

/createEnclosure – Create new enclosure

/createAnimal – Add new animal

/editAnimal/{id} – Edit existing animal

/archivedAnimals – View archived animals

⚠️ Access to creation and editing routes is restricted to admin users.

📁 Project Structure
app/ – Application logic

routes/ – Web routes

resources/views/ – Blade templates

public/ – Public assets

database/ – Migrations and seeders

🛠 Features
User authentication

Admin-only access to CRUD operations

Animal archive management

Responsive UI

RESTful route structure

🤝 Contribution
This project was created as part of a university course. External contributions are not expected, but suggestions are welcome.
