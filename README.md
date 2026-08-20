```markdown
# Anjali — Integrated Therapy Clinic Management System

A mobile-first therapy clinic management system that streamlines patient booking, therapist scheduling, medical records, and multi-branch clinic operations.

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-316192?style=for-the-badge&logo=postgresql&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Alpine.js](https://img.shields.io/badge/Alpine.js-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white)

---

## Overview

**Anjali** is a mobile-first clinic management system designed for therapy clinics managing multiple therapists, patients, and branches (*Kolaborasi*). 

The system covers the complete operational workflow—from patient registration and appointment booking to therapist medical records and branch administration. Unlike traditional duration-based scheduling, Anjali utilizes a **capacity-based session system**, enabling therapists to define available patient quotas per session rather than rigid appointment timeframes.

---

## Key Features

### Patient
* Phone number registration & Date of Birth login
* Multi-patient booking under a single transaction
* Multi-service selection per patient
* Payment proof upload
* Reschedule and cancellation workflows
* Booking history and medical record summary access
* Referral point system

### Therapist
* Weekly working schedule management
* Automatic recurring session generation
* Daily appointment schedule overview
* In-session medical record creation (EMR)
* Complete patient treatment history tracking

### Branch Admin (Admin Kolaborasi)
* Operational overview dashboard
* Booking approvals and rejections
* Patient, therapist, and service directory management
* Employee assignments and branch operational hours configuration

### Global Admin
* Multi-branch creation and configuration
* Branch administrator provisioning
* High-level global clinic analytics

---

## System Roles

| Role | Responsibility |
| :--- | :--- |
| **Admin Global** | Manages branches and provisions branch administrators |
| **Admin Kolaborasi** | Oversees day-to-day branch clinic operations |
| **Therapist** | Manages working availability, sessions, and medical records |
| **Patient** | Books appointments and tracks personal treatment history |

---

## Workflows

### 1. Main Operational Workflow
```text
Admin Global ──► Create Branch ──► Assign Admin ──► Branch Setup
                                                          │
Patient History ◄── Medical Record ◄── Therapy Session ◄── Booking & Payment

```

### 2. Booking Journey

```text
[Patient]                                [Admin]                    [Therapist]
Login                                       │                            │
  │                                         │                            │
Choose Therapist & Session                  │                            │
  │                                         │                            │
Add Patient(s) & Service(s)                 │                            │
  │                                         │                            │
Upload Payment Proof                        │                            │
  │                                         │                            │
Submit Booking ──────────────────────► Review Booking                    │
                                            │                            │
                                   [Approve / Reject]                    │
                                            │                            │
                                            └────────────────────► Today's Schedule
                                                                         │
                                                                   Start Session
                                                                         │
                                                                   Record EMR
                                                                         │
                                                                   Complete

```

---

## Capacity-Based Scheduling

Instead of fixed appointment lengths, Anjali allocates therapist slots using session capacity limits.

### Dynamic Capacity Tracking

* **Session Base Capacity:** 10 Patients
* **Approved Bookings:** 7 Patients
* **Live Remaining Capacity:** 3 Patients

Session generation is automated based on weekly availability rules. When operational hours change, future unbooked sessions adapt while preserving historical session logs.

---

## Medical Records (EMR) & Data Access

Therapists record clinical data during active therapy sessions. Once the session is marked completed, records become strictly read-only.

| Data Field | Patient | Therapist | Admin |
| --- | --- | --- | --- |
| **Therapy Goals** | :white_check_mark: | :white_check_mark: | :white_check_mark: |
| **Recommendations** | :white_check_mark: | :white_check_mark: | :white_check_mark: |
| **Clinical Record Summary** | Partial | :white_check_mark: | :white_check_mark: |
| **Internal Therapist Notes** | :x: | :white_check_mark: | :x: |

---

## Database Architecture

```text
users
├── pasiens
├── karyawans
│     ├── therapist_schedules
│     └── terapis_sesi
kolaborasi
├── layanan
├── terapis_layanan
└── cabang
booking
├── booking_pasien
├── rekam_medis
└── booking_reschedule_histories

```

| Table | Purpose |
| --- | --- |
| `users` | Core authentication & user identities |
| `pasiens` | Patient profiles and contact information |
| `karyawans` | Employee and therapist profiles |
| `kolaborasi` | Branch clinic entity mapping |
| `layanan` | Service catalog and pricing |
| `therapist_schedules` | Base recurring weekly therapist rules |
| `terapis_sesi` | Generated daily operational sessions |
| `booking` | Parent transaction records |
| `booking_pasien` | Itemized patients and chosen services per booking |
| `rekam_medis` | Clinical observations, metrics, and therapy outcomes |
| `booking_reschedule_histories` | Audit logs for schedule changes |

---

## Tech Stack

* **Backend:** Laravel 12, PHP 8.2+, PostgreSQL
* **Frontend:** Blade, Tailwind CSS, Alpine.js, Vite
* **Architecture:** MVC, Eloquent ORM, Database Transactions, Capacity-Driven Scheduling

---

## Getting Started

### Prerequisites

* PHP >= 8.2
* Composer
* Node.js & NPM
* PostgreSQL

### Installation

1. **Clone the repository:**
```bash
git clone [https://github.com/yourusername/anjali.git](https://github.com/yourusername/anjali.git)
cd anjali

```


2. **Install dependencies:**
```bash
composer install
npm install

```


3. **Set up environment variables:**
```bash
cp .env.example .env
php artisan key:generate

```


4. **Configure PostgreSQL database in `.env`:**
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=anjali_db
DB_USERNAME=your_username
DB_PASSWORD=your_password

```


5. **Run migrations and seeders:**
```bash
php artisan migrate --seed

```


6. **Start development servers:**
```bash
npm run dev

```


*In a separate terminal:*
```bash
php artisan serve

```



---

## Demo Accounts

> Login credential format: **Phone Number** and **Date of Birth**.

| Role | Demo Phone Number |
| --- | --- |
| **Admin Global** | `081234567001` |
| **Admin Kolaborasi** | `081234561001` |
| **Therapist** | `081234562001` |
| **Patient** | `081234570001` |

---

## Roadmap

* [ ] Loyalty points redemption catalog
* [ ] Digital prescription PDF attachments
* [ ] WhatsApp/Email automated booking notifications
* [ ] Therapist performance analytics and commission metrics
* [ ] Patient feedback and review scoring system

```

```
