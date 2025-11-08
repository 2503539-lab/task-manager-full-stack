# Task Manager - Full Stack Development Project

A modern, secure task management web application built with PHP, MySQL, Bootstrap 5, and AJAX.

## 🚀 Features

- ✅ **CRUD Operations**: Create, Read, Update, Delete tasks
- ✅ **Security**: Protection against XSS and SQL Injection
- ✅ **AJAX**: Real-time task status updates without page reload
- ✅ **Responsive Design**: Mobile-friendly Bootstrap 5 UI
- ✅ **Search Functionality**: Search tasks by multiple criteria
- ✅ **Priority Levels**: Low, Medium, High priority tasks
- ✅ **Status Tracking**: Pending and Completed tasks
- ✅ **Statistics Dashboard**: Visual overview of task counts

## 📋 Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Modern web browser

## 🛠️ Installation

### 1. Clone the repository
```bash
git clone https://github.com/YOUR_USERNAME/task-manager.git
cd task-manager
```

### 2. Configure Database
1. Create a MySQL database
2. Copy `config/db.php.example` to `config/db.php`
3. Edit `config/db.php` with your database credentials:
```php
$servername = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "your_database";
```

### 3. Import Database
Run the SQL file to create the tasks table:
```bash
mysql -u your_username -p your_database < database.sql
```

Or import via phpMyAdmin:
- Open phpMyAdmin
- Select your database
- Click "Import"
- Choose `database.sql`
- Click "Go"

### 4. Deploy to Server
Upload all files to your web server (e.g., `htdocs`, `public_html`, or `www` folder)

### 5. Access the Application
Open your browser and navigate to:
```
http://localhost/task_manager/
```

## 📁 Project Structure

```
task_manager/
│
├── config/
│   ├── db.php              # Database connection (not in git)
│   └── db.php.example      # Database template
│
├── includes/
│   ├── header.php          # Common header
│   └── footer.php          # Common footer
│
├── ajax/
│   └── update_status.php   # AJAX status handler
│
├── index.php               # Main task list page
├── add_task.php            # Add task handler
├── edit_task.php           # Edit task page
├── delete_task.php         # Delete task handler
├── search.php              # Search functionality
├── style.css               # Custom styles
├── script.js               # AJAX & JavaScript
└── database.sql            # Database schema
```

## 🔒 Security Features

- **Prepared Statements**: Protection against SQL Injection
- **Input Sanitization**: XSS prevention with `htmlspecialchars()`
- **Input Validation**: Server-side validation of all inputs
- **CSRF Protection**: Form validation and proper HTTP methods
- **Password Protection**: Database credentials not in repository

## 💻 Usage

### Add a Task
1. Click "Add New Task" button
2. Fill in task details (title, description, priority, due date)
3. Click "Save Task"

### Edit a Task
1. Click the edit icon (pencil) on any task
2. Modify task details
3. Click "Update Task"

### Delete a Task
1. Click the delete icon (trash) on any task
2. Confirm deletion

### Toggle Task Status
- Click the toggle switch to mark task as completed/pending (AJAX - no page reload)

### Search Tasks
- Navigate to Search page
- Enter search criteria
- Filter by status, priority, or date range

## 🎨 Technologies Used

- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **Framework**: Bootstrap 5.3
- **AJAX**: jQuery
- **Icons**: Bootstrap Icons

## 📝 Assignment Requirements Met

✅ PHP and MySQL implementation  
✅ CRUD operations (Create, Read, Update, Delete)  
✅ Search functionality with multiple criteria  
✅ Security (XSS and SQL Injection protection)  
✅ AJAX for dynamic updates  
✅ Responsive, modern UI  


## 📄 License

This project is for educational purposes as part of the Full Stack Development course (5CS045/UM1).

