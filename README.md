# Capstone-Project
Google CIA Project
STUDENT REGISTRATION README FILE


🎓 Student Event Registration System
A simple web-based application to register students for events using HTML, PHP, and MySQL (XAMPP).

📌 Features
* Student registration form
* Stores student & event details in MySQL database
* Uses PHP MySQL prepared statements
* File upload support (ID proof)
* Form validation

🛠️ Technologies Used
* HTML, CSS, JavaScript
* PHP
* MySQL
* XAMPP (Apache + MySQL)

⚙️ Setup Instructions (Mac - XAMPP)
1️⃣ Install XAMPP
* Download from: https://www.apachefriends.org
* Install and open XAMPP
* Start:
    * ✅ Apache
    * ✅ MySQL

2️⃣ Move Project Folder
* Copy your project folder (e.g. event_registration)
* Paste it into:
/Applications/XAMPP/xamppfiles/htdocs/

3️⃣ Create Database
1. Open browser:
http://localhost/phpmyadmin
1. Create a database:
student_events
1. Create table (example):
CREATE TABLE registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(15),
    dob DATE,
    gender VARCHAR(10),
    student_id VARCHAR(50),
    department VARCHAR(100),
    year_sem VARCHAR(20),
    cgpa FLOAT,
    advisor VARCHAR(100),
    event_name VARCHAR(100),
    event_date DATE,
    participation_type VARCHAR(50),
    team_name VARCHAR(100),
    interests TEXT,
    remarks TEXT,
    id_proof_path VARCHAR(255)
);

4️⃣ Configure Database Connection
Open your PHP file (e.g. register.php) and set:
$conn = new mysqli("localhost", "root", "", "student_events");

5️⃣ Run the Project
Open browser and go to:
http://localhost/event_registration/index.html

⚠️ Common Errors & Fixes
❌ 1. bind_param error
✔ Ensure number of s matches number of variables

❌ 2. Incorrect integer value
✔ Change column type to VARCHAR

❌ 3. Incorrect date value
✔ Use correct format:
$dob = date('Y-m-d', strtotime($_POST['dob']));

❌ 4. 500 Internal Server Error
✔ Check:
* Apache running
* PHP syntax errors
* File permissions

📁 Project Structure
event_registration/
│── index.html
│── register.php
│── uploads/
│── css/
│── js/

🚀 Future Improvements
* Login system (Admin)
* View registered students
* Edit/Delete records
* Better UI design

👨‍💻 Author
VRAJ PATEL 

📌 Notes
* Make sure MySQL is running before submitting form
* Always validate inputs before inserting into database
* Use prepared statements (already implemented)

