# Home-Service-Web-Application
 SPI Home Service Management System is a fully web-based platform designed to connect customers with skilled service workers for various home services. It supports seamless interaction between customers, workers, and administrators through dedicated interfaces and provides tools for service booking, profile management, and administrative oversight.

# SPI Home Service Management System
SPI Home Service Management System* is a full-stack web application designed to streamline the process of booking and managing home services such as plumbing, electrical, cleaning, and more. It connects customers with verified service workers and provides a powerful admin panel for system management.

# Project Overview
This system offers separate portals for:
- Customers to register, log in, book services, and leave feedback.
- Workers to manage their profile, accept bookings, and upload work proof.
- Admins to manage users, services, bookings, and reports.

# Features
## Customer Panel
- Register/Login
- Book a Service (Date, Time, Location)
- View Booking History
- Rate & Review Workers
- Download Service Invoices (PDF)
- Notification
## Worker Panel
- Register/Login
- Manage Profile, Categories, and Working Hours
- View and Accept/Reject Bookings
- Upload Work Proof
- View Ratings and Earnings
- Notification
## Admin Panel
- Dashboard Overview
- Manage Customers, Workers, and Services
- View Ratings and Work Proofs
- Handle Customer Support & FAQs
- Add Contact Info (Email, Phone, Address, Working Hours)
- Generate Reports
- Notification

## Tech Stack
| Layer        | Technology                |
|--------------|---------------------------|
| Frontend     | HTML, CSS, JavaScript     |
| Backend      | PHP                       |
| Database     | MySQL                     |
| Testing      | Selenium WebDriver, JUnit |
| Tools Used   | ChromeDriver, Apache, XAMPP |

## Automated Test Cases
Test cases implemented using *Selenium WebDriver (Java)* include:

1.  Customer Login Test  
2.  Admin Adding New Contact  
3.  Worker Login Test  
4.  Add New FAQ and Verify  
5.  Input Validation on Registration Form  

## Folder Structure
HomeServiceManagementSystem/ 
├── /Extra 
├── /Image 
       /src
├── /uploads 
       /admin
       /proofs
       /src
├── /files(php,css,js,html)              

##  How to Run Locally
1. Install XAMPP and start Apache & MySQL.
2. Clone this repository into htdocs.
3. Import the hsms.sql file into phpMyAdmin.
4. Update database config in /db/connection.php.
5. Visit http://localhost/HomeServiceManagementSystem/.

##  How to Run Tests
1. Download [ChromeDriver](https://sites.google.com/chromium.org/driver/)
2. Install Java & Maven.
3. Navigate to the test folder.
4. Run:

## Author
Seema Parwin  
Email: sheema2374@gmail.com  
LinkedIn: https://www.linkedin.com/in/seema-parwin-81851a305 
GitHub: https://github.com/seema01-parwin
