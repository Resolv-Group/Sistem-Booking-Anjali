```markdown
# 🏥 Anjali — Integrated Therapy Clinic Management System

> A mobile-first therapy clinic management system engineered to streamline patient bookings, therapist capacity scheduling, electronic medical records (EMR), and multi-branch clinic operations.

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel_12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP_8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-316192?style=for-the-badge&logo=postgresql&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Alpine.js](https://img.shields.io/badge/Alpine.js-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-646CFF?style=for-the-badge&logo=vite&logoColor=white)

</div>

---

## 📌 Overview

**Anjali** is a mobile-first clinic management system built for therapy centers operating across single or multiple branches (*Kolaborasi*). 

The platform covers the complete healthcare operational lifecycle—from lightweight mobile patient onboarding and multi-patient bookings to real-time session capacity tracking and immutable in-session EMR logging.

Unlike rigid duration-based calendars, Anjali implements a **Dynamic Capacity Engine**: therapists define patient limits per session, allowing multiple concurrent or queued treatments without slot fragmentation.

---

## ✨ Key Features

### 👤 Patient Portal (Mobile-First)
* 📱 **Frictionless Auth:** Fast phone number registration and Date of Birth (DOB) authentication.
* 👥 **Multi-Patient Booking:** Book appointments for multiple family members or groups in a single checkout.
* 💆 **Flexible Services:** Assign distinct therapy services per individual patient.
* 💳 **Payment Verification:** Integrated bank transfer proof upload and status tracker.
* 🔄 **Self-Service Rescheduling:** Automated rescheduling and cancellation pipelines.
* 📋 **Treatment Passport:** Direct access to medical record summaries and prescription advice.
* 🎁 **Referral System:** Earn clinic reward points per successful patient onboarding.

### 🩺 Therapist Workspace
* 📅 **Weekly Availability Engine:** Set repeating operational hours and session capacities.
* ⚡ **Auto Session Generator:** Recurring session calculation with zero schedule overlap.
* ⏱️ **Daily Rundown:** Live daily patient queue with real-time check-in alerts.
* 📝 **In-Session EMR:** Record vitals, pain scales, treatment goals, and private clinical notes.
* 📜 **Historical Logs:** Instant lookup for patient therapy history and past interventions.

### 🏢 Branch Admin (*Admin Kolaborasi*)
* 📊 **Operational Dashboard:** Real-time visibility into branch capacity, revenue, and queue status.
* ✅ **Booking Control Center:** Review, approve, or reject incoming reservations.
* 📂 **Directory Management:** Manage branch services, pricing, therapists, and staff assignments.
* ⏰ **Branch Rules:** Configure localized operating schedules and holiday overrides.

### 🌐 Global Admin (*HQ*)
* 🏛️ **Multi-Branch Provisioning:** Deploy and configure new clinic branches (*Kolaborasi*).
* 🛡️ **Role & Access Governance:** Assign branch managers, therapists, and administrative staff.
* 📈 **Cross-Branch Analytics:** Centralized overview of network-wide operational health.

---

## 👥 System Roles

| Role | Badge | Operational Scope |
| :--- | :---: | :--- |
| **Admin Global** | `🌐 HQ` | Branch provisioning, global governance, system-wide analytics |
| **Admin Kolaborasi** | `🏢 Branch` | Daily branch appointments, booking verification, staff rosters |
| **Therapist** | `🩺 Medical` | Working availability, active therapy sessions, EMR management |
| **Patient** | `👤 Client` | Mobile reservations, service selection, EMR summaries |

---

## 🔄 Core Workflows

### 🏥 End-to-End Operational Lifecycle
```text
🌐 HQ Admin Setup       🏢 Branch Management              👤 Patient Journey
┌──────────────────┐    ┌─────────────────────┐          ┌────────────────────┐
│  Create Branch   │─►──│ Assign Branch Admin │─►────────│ Register via Phone │
└──────────────────┘    └─────────────────────┘          └─────────┬──────────┘
                                                                   │
┌──────────────────┐    ┌─────────────────────┐          ┌─────────▼──────────┐
│ Patient History  │◄───│ Complete Session &  │◄─────────│ Book Appointment & │
│  & EMR Updated   │    │ Create EMR Record   │          │ Upload Pay Proof   │
└──────────────────┘    └─────────────────────┘          └────────────────────┘
                               ▲                                   │
                               │                                   ▼
                        🩺 Therapist Queue               🏢 Admin Verification
                        ┌─────────────────────┐          ┌────────────────────┐
                        │ Start Therapy Batch │◄─────────│ Approve / Confirm  │
                        └─────────────────────┘          └────────────────────┘

```

---

## ⚡ Capacity-Based Scheduling Logic

Traditional clinic systems lock time slots (e.g., 30 mins). Anjali uses **Session Capacity Quotas** to maximize clinic efficiency:

```text
┌────────────────────────────────────────────────────────┐
│ Session: Monday Batch A (08:00 - 10:00)                │
│ Base Capacity: 10 Patients                             │
├───────────────────────────────┬────────────────────────┤
│ ██████████████░░░░░░          │ 7 Approved (Occupied)  │
│ ░░░░░░                        │ 3 Remaining Slots      │
└───────────────────────────────┴────────────────────────┘

```

* **Dynamic Capacity Recalculation:** Adjusts instantly as admins approve bookings.
* **Audit-Preserved History:** Modifying future schedules never alters historical session records.

---

## 🔒 Medical Records (EMR) & Privacy Matrix

Clinical data is captured dynamically during treatment and permanently locked (*Read-Only*) once marked complete.

| Data Metric | 👤 Patient | 🩺 Therapist | 🏢 Branch Admin | 🌐 HQ Admin |
| --- | --- | --- | --- | --- |
| **Therapy Goals** | 👁️ View | ✏️ Write / View | 👁️ View | 👁️ View |
| **Care Recommendations** | 👁️ View | ✏️ Write / View | 👁️ View | 👁️ View |
| **Clinical Summary (Vitals/Scale)** | 👁️ Partial | ✏️ Write / View | 👁️ View | 👁️ View |
| **Internal Therapist Notes** | ❌ Hidden | ✏️ Write / View | 🔒 Read-Only | ❌ Hidden |

---

## 🗄️ Database Architecture

```text
📦 Database Schema
 ├── 👥 users
 │    ├── 👤 pasiens
 │    └── 🩺 karyawans
 │          ├── 📅 therapist_schedules
 │          └── ⏱️ terapis_sesi
 ├── 🏢 kolaborasi
 │    ├── 🏷️ layanan
 │    ├── 🔗 terapis_layanan
 │    └── 📍 cabang
 └── 📑 booking
      ├── 👥 booking_pasien
      ├── 📝 rekam_medis
      └── 📜 booking_reschedule_histories

```

---

## 💻 Tech Stack & Architecture

* **Backend Engine:** Laravel 12 (PHP 8.2+)
* **Database Layer:** PostgreSQL (BYTEA storage, strict transactional integrity)
* **Frontend UI:** Blade Templates, Tailwind CSS (Mobile-First UI), Alpine.js
* **Asset Pipeline:** Vite
* **Design Patterns:** MVC, Repository/Service Separation, Event-Driven Capacity Updates

---

## 🚀 Getting Started

### Prerequisites

* **PHP** $\ge$ 8.2
* **Composer**
* **Node.js** & **NPM**
* **PostgreSQL** $\ge$ 14

### Installation

```bash
# 1. Clone repository
git clone [https://github.com/yourusername/anjali.git](https://github.com/yourusername/anjali.git)
cd anjali

# 2. Install dependencies
composer install
npm install

# 3. Environment configuration
cp .env.example .env
php artisan key:generate

# 4. Configure PostgreSQL in .env
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=anjali_db
# DB_USERNAME=postgres
# DB_PASSWORD=yourpassword

# 5. Database setup & seeding
php artisan migrate --seed

# 6. Run application
npm run dev
# In another terminal:
php artisan serve

```

---

## 🔑 Demo Access Credentials

> Authentication uses **Phone Number** and **Date of Birth (`YYYY-MM-DD`)**.

| Role | 📱 Demo Phone | 🎂 Default DOB |
| --- | --- | --- |
| **🌐 Admin Global** | `081234567001` | `1990-01-01` |
| **🏢 Admin Kolaborasi** | `081234561001` | `1992-05-15` |
| **🩺 Therapist** | `081234562001` | `1995-08-20` |
| **👤 Patient** | `081234570001` | `2000-12-10` |

```

```
