# Fitness App - AI-Powered Fitness Tracker (Backend API) 🏋️‍♂️🤖

This repository contains the robust, scalable Backend RESTful APIs for **Fitness App**, an AI-powered fitness tracking application. The system leverages modern software architecture to support real-time motion analysis, exercise form correction, and personalized fitness tracking.

---

### 🌟 Key Features (Backend Capabilities)

- **RESTful API Architecture:** Designed and implemented clean, standardized APIs for seamless integration with mobile (Flutter) and AI microservices.
- **Robust Authentication:** Secure user authentication and management system, including advanced security fixes (e.g., OTP vulnerability patching for password resets).
- **Database Management:** Optimized relational schema designed with complex migrations to handle high-frequency fitness metrics data efficiently.
- **Real-time Data Support:** Built backend structures capable of handling rapid AI inference outputs to provide real-time form correction feedback to users.

---

### 🛠️ Tech Stack & Architecture

- **Language:** PHP 8.2+
- **Framework:** Laravel (MVC Architecture)
- **Database:**MongoDB
- **API Testing & Documentation:** Postman
- **Deployment:** Railway / Production Cloud Environments

---

### 🚀 Getting Started & Installation

To get a local copy up and running, follow these simple steps:

1. **Setup & Run Commands:**
   ```bash
   git clone [https://github.com/zeinak112/gp-backend.git](https://github.com/zeinak112/gp-backend.git)
   composer install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate
   php artisan serve
