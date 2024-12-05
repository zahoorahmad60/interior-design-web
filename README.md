# Interior Design Web Application

## Overview

The **Interior Design Web Application** is a platform facilitating collaboration between clients and interior designers. The application supports functionalities like client-designer chats, project management, order placement, user profiles, and more. 

## Features

- **User Roles**: Separate dashboards for clients and designers.
- **Authentication**: Login and signup for both clients and designers.
- **Chat System**: Real-time messaging between clients and designers.
- **Project Management**: View, add, and manage design projects.
- **Order Management**: Place and track orders for interior design services.
- **Feedback**: Clients can provide feedback on projects.
- **Payment Integration**: Payment functionality for orders.
- **Notifications**: Updates on new messages, projects, and orders.

## Project Structure

The project comprises the following components:

- **Frontend**: HTML, CSS, and PHP for the user interface and logic.
  - `styles.css`: Contains styles for the application.
  - `index.php`: Home page of the application.
  - `header.php`: Reusable header template.

- **Backend**: PHP scripts for functionality and database interactions.
  - `db_connection.php`: Database connection logic.
  - `config.php`: Configuration settings for the project.

- **Database**: SQL files to set up the necessary tables.
  - `interior_design2.sql`

- **Assets**: Contains images, icons, and other static resources.
  - `logo.png`
  
- **Uploads**: Folder for storing uploaded files.

## Installation

1. **Prerequisites**:
   - PHP (>=7.0)
   - MySQL or MariaDB
   - A web server (e.g., Apache or Nginx)
   
2. **Setup**:
   - Clone or download the project files.
   - Place the project in the server's root directory (e.g., `htdocs` for XAMPP).
   - Import the database:
     - Open a MySQL client.
     - Run the scripts in `interior_design1.sql` and `interior_design2.sql` to set up the database.
   - Configure the database:
     - Update `db_connection.php` with your database credentials.

3. **Run the Application**:
   - Start your web server and database.
   - Access the application via `http://localhost/Interior_design`.

## Usage

1. **Clients**:
   - Register or log in via `client_login.php`.
   - Browse and select projects, chat with designers, and manage orders.

2. **Designers**:
   - Register or log in via `designer_login.php`.
   - Manage profiles, interact with clients, and submit designs.

## Contributing

We welcome contributions! Follow these steps:

1. Fork the repository.
2. Create a new branch for your feature or bug fix.
3. Submit a pull request with a detailed description.

## Licensing

This project is under [Zahoor Ahmad](https://github.com/zahoorahmad60)
