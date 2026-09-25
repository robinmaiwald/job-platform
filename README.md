# Job Platform

## Contents

1. [Overview](#1-overview)
2. [Tech Stack](#2-tech-stack)
3. [Features](#3-features)
4. [Architecture](#4-architecture)
5. [API](#5-api)
6. [Authentication](#6-authentication)
7. [Database](#7-database)
8. [Frontend](#8-frontend)
9. [Project Structure](#9-project-structure)
10. [Setup & Development](#10-setup--development)
11. [Extras] (#11-extras)

---

## 1. Overview

This project is a full-stack job platform built with PHP/Laravel and Vue. It provides a REST API for managing users, companies, and jobs, together with a Vue/Inertia.js frontend for public users (guests), authenticated users (users), and administrators (admins).

The platform has three different access levels: **Guest**, **User**, and **Admin**, each with different permissions and functionality. These access rights are explained in more detail later in the documentation.

The backend is structured around Laravel Controllers, Form Requests, Policies, Repositories, Eloquent Models, and API Resources. Each layer has a specific responsibility within the request flow. Authentication is handled with Laravel Sanctum, with separate token abilities for normal users and administrators.

The frontend is built with Vue 3, Inertia, and Tailwind CSS and provides an interface for all three access types.

### Case Study

The original case study for this project can be found [here](TenMedia_CaseStudy.pdf).
The project was developed based on the requirements described in the case study.

---

## 2. Tech Stack

### Backend

- **PHP**
- **Laravel**
- **Laravel Sanctum** — API authentication and token abilities

### Frontend

- **Vue 3** 
- **Inertia.js** 
- **Tailwind CSS** 
- **TypeScript** 
- **Vite**

---

## 3. Features

### Models

#### User
- Manage their profile
- Create and manage companies
- Create and manage jobs 
- Belongs to many companies
- Has many jobs

#### Company
- Can have an owner
- Have multiple users as members
- Have multiple jobs
- Can be created and managed by owner or administrators.

#### Job
- Belongs to a company;
- Has a creator;
- Contains a title, description, and location;
- Can be created and managed by creator or administrators.

#### Admin
- Can view and manage their own profile
- Full CRUD access to User, Company, Job

### Relationships

```text
User
 ├── belongs to many Companies
 └── has many Jobs

Company
 ├── has many Users
 ├── has one Owner (User)
 └── has many Jobs

Job
 ├── belongs to one Company
 └── belongs to one User (Creator)

```
### CRUD Rights


<details>
<summary><strong>Guest</strong></summary>

| Operation | Users | Companies | Jobs | Admin |
|:---|:---:|:---:|:---:|:---:|
| **Create** | ✓ Register | — | — | — |
| **Read** | — | ✓ | ✓ | — |
| **Update** | — | — | — | — |
| **Delete** | — | — | — | — |

</details>

<br>

<details>
<summary><strong>User</strong></summary>

| Operation | Users | Companies | Jobs | Admin |
|:---|:---:|:---:|:---:|:---:|
| **Create** | — | ✓ | ✓ | — |
| **Read** | Own profile | ✓ | ✓ | — |
| **Update** | Own profile | Member | Own jobs | — |
| **Delete** | Own profile | Owner | Own jobs | — |

</details>
<br>

<details>
<summary><strong>Admin</strong></summary>

| Operation | Users | Companies | Jobs | Admin |
|:---|:---:|:---:|:---:|:---:|
| **Create** | ✓ | ✓ | ✓ | — |
| **Read** | ✓ | ✓ | ✓ | Own profile |
| **Update** | ✓ | ✓ | ✓ | Own profile |
| **Delete** | ✓ | ✓ | ✓ | — |

</details>
<br>

---
## 4. Architecture

The backend follows a layered architecture where each layer has a specific responsibility. 
### Request Flow

```text
HTTP Request
     ↓
Form Request
     ↓
Controller
     ↓
   Policy
     ↓
 Repository
     ↓
 API Resource
     ↓
JSON Response
```

`Form Requests` are responsible for validating incoming HTTP data

`Controllers` handle the HTTP request and coordinate the different application layers.

`Policies` handle authorization rules for Users, Companies, Jobs, and Admins.

`Repositories` handle database operations and data access

`API Resources` control how models and relationships are transformed into JSON responses returned by the API.


#### Authorization

Authorization is handled through Laravel Policies and Sanctum token abilities.

- **Guest** — can access public resources and register an account.
- **User** — authenticated with a user token ability and restricted by resource policies.
- **Admin** — authenticated with an admin token ability and has administrative access to Users, Companies, and Jobs.

Sanctum abilities provide the first access boundary, while Policies handle resource-level authorization.

---

## 5. API

The application provides a RESTful API for managing Users, Companies, and Jobs.

### RESTful API

The API follows standard REST conventions. Resources are represented by URLs, while HTTP methods define the requested operation.

| Method | Operation | Example |
|:---|:---|:---|
| `GET` | List resources | `GET /jobs` |
| `GET` | Retrieve a resource | `GET /jobs/{id}` |
| `POST` | Create a resource | `POST /jobs` |
| `PUT` | Update a resource | `PUT /jobs/{id}` |
| `DELETE` | Delete a resource | `DELETE /jobs/{id}` |

Only the operations required by the application are exposed. Unsupported or unauthorized operations are not implemented as accessible API routes.

The API returns JSON responses and uses standard HTTP status codes to communicate the result of each request.

### Access Control

API access is divided into three levels:

- **Guest** — can access public resources and register an account.
- **User** — can access authenticated User endpoints according to resource policies.
- **Admin** — can access administrative endpoints for Users, Companies, and Jobs.

---

## 6. Authentication

Authentication is handled using Laravel Sanctum with separate authentication flows for Users and Admins.

### User Authentication

Users authenticate through the `/api/login` endpoint using their email and password.

After successful authentication, the API returns a Sanctum personal access token with the `user` ability.

### Admin Authentication

Admins authenticate through the `/api/admin/login` endpoint using their admin credentials.

After successful authentication, the API returns a Sanctum personal access token with the `admin` ability.

### Token Abilities

User and Admin tokens use separate Sanctum abilities to keep the two authentication contexts isolated.

| Token | Ability | Access |
|---|---|---|
| User token | `user` | User endpoints |
| Admin token | `admin` | Admin endpoints |

A token must have the required ability to access a protected endpoint. 
Policies provide an additional layer of resource-level authorization after authentication.


---

## 7. Database

The application uses a relational database managed through Laravel migrations and Eloquent ORM.

### Application Tables

- **users**
- **admins**
- **companies**
- **jobs**
- **company_user**

### Laravel Infrastructure

- **personal_access_tokens** — used by Laravel Sanctum for API authentication.

### Factories & Seeders

Laravel factories are used to generate test and development data for users, companies, and jobs.

The database seeders create a default development dataset, including an administrator and related users, companies, and jobs. This provides a consistent starting point when setting up the project locally.

Factories can also be used independently in tests to create the required models and relationships for individual test cases.

---

## 8. Frontend

The frontend is built with Vue 3, Inertia.js, TypeScript, and Tailwind CSS.

Inertia connects the Vue frontend with the Laravel backend without requiring a separate frontend API client for page navigation. Laravel handles routing and server-side data preparation, while Vue is responsible for rendering the user interface.

### Frontend Structure

The frontend is organized into reusable components, pages, and shared types.

```text
resources/js/
├── components/    # Reusable Vue components
├── pages/         # Application pages
│   ├── admin/
│   ├── auth/
│   └── public/
└── types/         # Shared TypeScript types
```
The frontend provides separate interfaces for Guests, Users, and Admins, following the same authorization boundaries as the backend.

## 9. Project Structure

The project follows Laravel's standard application structure, with the main application logic organized into Controllers, Form Requests, Policies, Repositories, Models, API Resources, and Vue pages.

```text
app/
├── Http/
│   ├── Controllers/
│   ├── Requests/
│   └── Resources/
├── Models/
├── Policies/
└── Repositories/

database/
├── factories/
├── migrations/
└── seeders/

resources/
└── js/
    ├── components/
    ├── pages/
    └── types/

routes/
├── api.php
└── web.php

tests/

```
---

## 10. Setup & Development

### Requirements

- PHP
- Composer
- Node.js
- npm
- SQLite

### Installation

Clone the repository and enter the project directory:

    git clone https://github.com/robinmaiwald/job-platform
    cd job-platform

Install the backend dependencies:

    composer install

Install the frontend dependencies:

    npm install

### Environment

Copy `.env.example` to `.env`.

**Linux/macOS:**

    cp .env.example .env

**Windows PowerShell:**

    Copy-Item .env.example .env

The project uses SQLite for local development. The default `.env.example` configuration already contains the required database settings:

    DB_CONNECTION=sqlite

Generate the Laravel application key:

    php artisan key:generate

### Database

Make sure the SQLite database file exists, then run the migrations and seed the development database:

    touch database/database.sqlite

    php artisan migrate --seed

For Image/Logo storage run:

    php artisan storage:link

On Windows PowerShell, the SQLite file can be created with:

    New-Item database/database.sqlite -ItemType File

The seed command creates the database tables and populates the database with the default development data.

### Running the Application

Start the Laravel development server:

    php artisan serve

In a separate terminal, start the Vite development server:

    npm run dev

The Laravel server handles the backend and API, while Vite handles the frontend development assets.

Once both servers are running, open the application in your browser:

http://localhost:8000

### Test Credentials and Tests

```
Test User Mail: test@example.com
Test User Password: password
```
```
Test Admin Name: admin
Test Admin Password: password
```

Run the complete test suite with:

    php artisan test


### API Testing

The API can be tested manually using tools such as cURL.

To test authenticated endpoints, first obtain a Sanctum token by logging in.

#### User Token

Send the User's email and password to:

    POST /api/login

Example:

    curl -X POST http://localhost:8000/api/login \
         -H "Content-Type: application/json" \
         -d '{
               "email": "test@example.com",
               "password": "password"
             }'

The response contains a Sanctum token with the `user` ability.

Use the returned token in the `Authorization` header:

    curl -X POST http://localhost:8000/api/jobs \
         -H "Authorization: Bearer <token>" \
         -H "Content-Type: application/json" \
         -d '{
               "company_id": 1,
               "title": "Backend Developer",
               "description": "Laravel backend development",
               "location": "Hannover"
             }'
#### Admin Token

Admins can obtain an `admin` token through:

    POST /api/admin/login

Example:

    curl -X POST http://localhost:8000/api/admin/login \
         -H "Content-Type: application/json" \
         -d '{
               "name": "admin",
               "password": "password"
             }'

The returned token can then be used for Admin endpoints.

## 11. Extras
This part is for later changes and notes.

### Company Logo Uploads
* Logos are optional when creating a company.
* Uploaded images are validated as JPEG, PNG, JPG, or WebP with a 2 MB maximum size.
* Files are stored using Laravel's public filesystem under `company-logos/`.
* Only the file path is stored in the database.
* Replacing a logo automatically removes the previous file.
* Companies without a logo display a fallback placeholder.
* Logo uploads are restricted by the existing company update authorization.


### Setup Verification

The project setup has been successfully tested from a fresh clone on both Linux and macOS.