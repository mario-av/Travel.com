# Traveldotcom - Internal Management System

![Traveldotcom Logo](img/traveldotcomlogo.png)

![Version](https://img.shields.io/badge/version-2.0.0-blue.svg) ![Build Status](https://img.shields.io/badge/build-passing-success.svg) ![License](https://img.shields.io/badge/license-MIT-blue.svg)

Welcome to the **Traveldotcom** repository. This platform is a robust, enterprise-grade solution designed for the internal management of vacation packages, bookings, and customer feedback. It leverages a strict Model-View-Controller (MVC) architecture with role-based access control (RBAC) to ensure data integrity and operational security.

## Table of Contents

1. [System Overview](#system-overview)
2. [Key Features](#key-features)
3. [Architecture & Design](#architecture--design)
4. [User Roles & Permissions](#user-roles--permissions)
5. [Operational Workflows](#operational-workflows)
6. [Installation & Deployment](#installation--deployment)
7. [Testing & Quality Assurance](#testing--quality-assurance)

---

## System Overview

Traveldotcom serves as the central orchestration layer for our travel products. It connects product managers (Advanced Users) with customers (Standard Users) while maintaining strict oversight through Administrative controls. The system is built to handle high-concurrency booking requests and complex state management for vacation packages (e.g., draft, pending approval, published).

## Key Features

- **Role-Based Access Control (RBAC)**: secure hierarchy ensuring users only access authorized resources.
- **Approval Workflow Engine**: Automated status transition for vacation packages created by non-admin staff.
- **Dynamic Booking System**: Real-time slot management and reservation tracking.
- **Moderated Review System**: Customer feedback is queued for administrative approval before public display.
- **Automated Testing Pipeline**: Integrated CI/CD using GitHub Actions for reliability.

---

## Architecture & Design

The application follows strict software engineering principles to ensure maintainability and scalability.

### Technology Stack

- **Backend**: Laravel 11.x (PHP 8.2+)
- **Database**: PostgreSQL
- **Frontend**: Blade Templating Engine
- **Styles**: TailwindCSS (with Bootstrap Icons)
- **Infrastructure**: Docker-ready, CI/CD pipelines via GitHub Actions

### Layered Structure

1.  **Routing Layer**: Handles incoming HTTP requests and directs them to appropriate controllers.
2.  **Middleware Layer**: Enforces security policies (Authentication, Role Verification, Email Verification) before requests reach the application logic.
3.  **Controller Layer**: Orchestrates business logic, validating input via FormRequests and interacting with the Data Layer.
4.  **Model/Data Layer**: Eloquent ORM classes representing business entities (`Vacation`, `Booking`, `User`) with defined relationships and casting.
5.  **View Layer**: Server-side rendered views ensuring SEO compliance and fast initial load times.

---

## User Roles & Permissions

The system defines four distinct user roles, each with specific capabilities:

| Role         | Access Level       | Description                                                                                                                     |
| :----------- | :----------------- | :------------------------------------------------------------------------------------------------------------------------------ |
| **Admin**    | System-Wide        | Full control over all resources. Can create, edit, delete, and **approve** any content. Manages users and reviews.              |
| **Advanced** | Restricted Creator | Can create and edit vacation packages. **New Items require Admin approval** before going live. Can only delete their own items. |
| **Verified** | Legacy Creator     | Similar to Advanced but operates under legacy permissions. Primarily manages their own existing catalog.                        |
| **User**     | Consumer           | Can browse the public catalog, make bookings, and submit reviews for vacations they have booked.                                |

---

## Operational Workflows

### 1. Vacation Creation Cycle

1.  **Drafting**: An _Advanced User_ submits a new vacation package.
2.  **Pending State**: The system saves the vacation with `approved = false`. It is not visible in the public catalog.
3.  **Review**: An _Admin_ reviews the pending item via the Dashboard.
4.  **Approval/Rejection**: The Admin publishes the item. It is now visible to _Standard Users_.

### 2. Booking Process

1.  **Availability Check**: System verifies `available_slots > 0` and date validity.
2.  **Slot Reservation**: Upon confirmation, the slot count is decremented atomically to prevent race conditions.
3.  **Record Creation**: A booking record links the User to the Vacation.

### 3. Review Moderation

1.  **Submission**: valid ONLY if the user has a verified booking for that vacation.
2.  **Queue**: Review is saved as `approved = false`.
3.  **Publication**: Admin authorizes the review, making it visible on the Vacation Detail page.

---

## Installation & Deployment

This guide assumes you have **PHP 8.2+**, **Composer**, and a valid SQL database server installed.

### 1. Get the Source Code

**Option A: Clone via Git**

```bash
git clone https://github.com/company/traveldotcom.git
cd traveldotcom
```

**Option B: Download Release**

1. Go to the **Releases** section in this repository.
2. Download the latest `.zip` source code (v2.0).
3. Extract and run in your server.

**Option C: Docker Package (GitHub Container Registry)**
If you prefer containers, you can pull the pre-built package:

```bash
docker pull ghcr.io/company/traveldotcom:latest
```

### 2. Install Dependencies

```bash
composer install --optimize-autoloader --no-dev
npm install && npm run build
```

### 3. Environment Configuration

Copy the example environment file and configure your database credentials.

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Database Initialization

Run the database migrations to create the schema and seed initial data.

```bash
php artisan migrate --seed
```

---

## Testing & Quality Assurance

### Automated Testing

The repository includes a comprehensive test suite managed by GitHub Actions. To run tests locally:

```bash
php artisan test
```

### Manual Testing (Seeders)

The `DatabaseSeeder` populates the database with users for each role to facilitate manual UAT (User Acceptance Testing).

**Default Password**: `password`

| Account Type | Email                 | Usage Scenario                               |
| :----------- | :-------------------- | :------------------------------------------- |
| **Admin**    | `admin@travel.com`    | Test content moderation and system settings. |
| **Advanced** | `advanced@travel.com` | Test creation workflow and approval gates.   |
| **Verified** | `verified@travel.com` | Test legacy management features.             |
| **User**     | `user@travel.com`     | Test booking flow and review submission.     |
