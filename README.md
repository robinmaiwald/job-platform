# Job Platform

## Contents

1. [Overview](#1-overview)
2. [Tech Stack](#2-tech-stack)
3. [Features](#3-features)
4. [Architecture](#4-architecture)
5. [Authentication](#5-authentication)
6. [API](#6-api)
7. [Frontend](#7-frontend)
8. [Database](#8-database)
9. [Project Structure](#9-project-structure)
10. [Design Decisions](#10-design-decisions)
11. [Setup & Development](#11-setup--development)

---

## 1. Overview

This project is a full-stack job platform built with Laravel and Vue. It provides a REST API for managing users, companies, and jobs, together with a Vue/Inertia frontend for public users, authenticated users, and administrators.

Users can register, manage their profile, create and manage companies and jobs, and browse publicly available companies and jobs. Administrators have a separate authentication flow and can manage users, companies, and jobs through dedicated admin endpoints and interfaces.

The backend is structured around Laravel's Eloquent models, Form Requests, Policies, Repositories, and API Resources. Authentication is handled with Laravel Sanctum, with separate token abilities for normal users and administrators.

The frontend is built with Vue 3, Inertia, and Tailwind CSS and provides the user-facing and administrative interfaces for the platform.

### Case Study

The project was developed as a case study to implement a complete job-platform backend and connect it to a functional frontend.

The main requirements were to:

- model users, companies, and jobs and their relationships;
- provide CRUD functionality with different permissions depending on the user role;
- implement separate authentication and access boundaries for users and administrators;
- enforce authorization through Laravel Policies;
- separate validation, request handling, persistence, and API response representation;
- provide a REST API that can be consumed by the frontend;
- implement the corresponding public, authenticated, and administrative frontend areas;
- provide a reproducible development and testing setup.

The implementation focuses on clear separation of responsibilities, explicit authorization rules, and a domain model that reflects the platform's business requirements.

---

## 2. Tech Stack

*To be added.*

---

## 3. Features

*To be added.*

---

## 4. Architecture

*To be added.*

---

## 5. Authentication

*To be added.*

---

## 6. API

*To be added.*

---

## 7. Frontend

*To be added.*

---

## 8. Database

*To be added.*

---

## 9. Project Structure

*To be added.*

---

## 10. Design Decisions

*To be added.*

---

## 11. Setup & Development

*To be added.*