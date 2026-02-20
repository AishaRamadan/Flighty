# ✈️ Flighty - Flight Booking System

Flighty is a web-based flight booking system built using PHP and MySQL.  
It allows users to register, log in, browse flights, and book tickets.  
The system also includes an admin panel to manage flights.

---

## 🚀 Features

### 👤 User Features
- User Registration & Login
- Secure Session Management
- Browse Available Flights
- Book Flights
- View Booked Flights
- Manage Account

### 🛠 Admin Features
- Add New Flights
- Update Flight Information
- Delete Flights
- Manage Flight Listings

---

## 🧰 Technologies Used

- PHP (Core PHP)
- MySQL
- HTML5
- CSS3
- JavaScript
- XAMPP

---

## 📂 Project Structure
Flighty/
│
├── Connection/
│ └── db-connection.php
│
├── style/
│ └── CSS files
│
├── assets/
│ └── images
│
├── login.php
├── signUp.php
├── MainPageUser.php
├── MainPageAdmin.php
├── accountUser.php
├── manageFlight.php
├── booked.php

---


---

## 🗄 Database

The system uses a MySQL database with the following main tables:

- **Users**: stores user info (id, name, email, password, role, etc.)
- **Flights**: stores flight info (id, source, destination, date, seats, etc.)
- **Bookings**: stores user bookings (id, user_id, flight_id, booking_date, etc.)

You can import the database using phpMyAdmin in XAMPP.

---

## ⚙️ How to Run the Project

1. Install **XAMPP**.
2. Move the project folder to the `htdocs/` directory.
3. Start **Apache** and **MySQL** in XAMPP.
4. Import the `Flighty` database via **phpMyAdmin**.
5. Open the project in a browser:


---

## 🔐 Authentication & Security

- Session-based authentication
- Role-based access (Admin / User)
- Form validation
- Prepared statements for database queries
- Redirect protection for restricted pages

