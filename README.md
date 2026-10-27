# Online Course Management System (PHP + MySQL)

## Requirements
- XAMPP (Apache, PHP 8+, MySQL/MariaDB)
- Web browser

## Setup
1. Copy the `online_course_management_system` folder into `C:\xampp\htdocs\`.
2. Start **Apache** and **MySQL** in XAMPP Control Panel.
3. Open `http://localhost/phpmyadmin`.
4. Select **Import**, choose `database.sql`, then click **Import** / **Go**.
5. Check `config.php`. Default XAMPP settings are host `localhost`, user `root`, blank password.
6. Open `http://localhost/online_course_management_system/`.

## Demo administrator
- Email: `admin@coursehub.test`
- Password: `Admin@123`

If the demo admin login fails, open a terminal in the project folder and run:
```bash
php -r "echo password_hash('Admin@123', PASSWORD_DEFAULT), PHP_EOL;"
```
Copy the generated hash into the admin row's `password_hash` field in phpMyAdmin, or update `database.sql` before importing it.

## Test the project
1. Log in as the demo administrator.
2. Add a course from the Admin Dashboard.
3. Log out and register a student using a different email address.
4. Log in as the student and enroll in a course.
5. Open **My Dashboard** to verify the enrollment.
6. Log out and log in as admin to view the updated enrollment count and student list.

## Main files
- `index.php`: public course catalogue
- `register.php`: student registration
- `login.php`, `logout.php`: authentication
- `admin.php`: admin dashboard, course create/delete, student list
- `enroll.php`: course enrollment handler
- `dashboard.php`: student's enrolled courses
- `config.php`: database connection and escaping helper
- `database.sql`: schema and starter data
- `project_report.docx`: project report
- `screenshots/`: illustrative sample screen designs

## Notes
This is an academic demonstration project. It does not include a payment gateway, video streaming, course lessons, certificates, email verification, or a password-reset workflow. Before public deployment, add CSRF protection, rate limiting, production configuration, HTTPS, and a more complete authorization/security review.
