# SubstitutionPlanner

SubstitutionPlanner is a web application designed to help manage and organize substitution schedules, particularly useful for schools or organizations needing to coordinate timetable changes and substitutions.

## Features

- **Login System**: Users can log in securely to access scheduling options.
- **Schedule Management**: Tools to manage and modify substitution schedules.
- **Database Integration**: Uses SQL scripts for managing and deleting schedule data.
- **Modular PHP Logic**: The core logic is separated into the `PHP_Logic` directory for better maintainability and scalability.
- **Responsive Interface**: Built with Bootstrap for modern, responsive design.

## Getting Started

### Prerequisites

- PHP 7.x or higher
- Web server (e.g., Apache, Nginx)
- MySQL database

### Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/DziedzicFilip/SubstitutionPlanner.git
   cd SubstitutionPlanner
   ```

2. **Set up the database:**
   - Import the SQL files (`zarzadzanie_harmonogramem2.sql` and `sql_delete.sql`) into your MySQL database.

3. **Configure your web server:**
   - Serve the repository root as your web document root.
   - Make sure PHP is enabled and configured correctly.

4. **Access the application:**
   - Open your browser and go to `http://localhost/index.html`
   - Click “Zaloguj się” to log in and manage schedules.



## Usage

1. Go to the main page.
2. Log in using your credentials.
3. Manage substitution schedules through the provided interface.


