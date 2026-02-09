# SQL Injection Lab

An interactive educational web application that demonstrates SQL Injection vulnerabilities and their mitigations. Built as a hands-on lab for the **Securite des Applications** module at **EMSI**.

![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green)
![Purpose](https://img.shields.io/badge/Purpose-Educational-red)

> **Warning:** This application is **intentionally vulnerable**. It is designed for local educational use only. Never deploy it on a public server.

---

## Labs Included

| #  | Lab                   | Attack Type                     | Technique                          |
|----|-----------------------|---------------------------------|------------------------------------|
| 1  | Login Bypass          | Authentication bypass           | `admin' --`                        |
| 2  | Data Extraction       | Dump all records + UNION SELECT | `' OR '1'='1`                      |
| 3  | Privilege Escalation  | UPDATE injection                | Modify role from `user` to `admin` |
| 4  | Delete Injection      | Mass data destruction           | `' OR '1'='1` in DELETE            |
| 5  | Blind SQL Injection   | Boolean-based data extraction   | `SUBSTRING(password,1,1)='a'`      |
| 6  | Secure Login          | Mitigation with prepared statements | PDO bound parameters           |

Each vulnerable page displays the **exact SQL query** that was executed, so you can see how the injection modifies the query structure in real time.

---

## Tech Stack

- **Backend:** PHP (PDO)
- **Database:** MySQL
- **Frontend:** HTML / CSS (no frameworks)

---

## Project Structure

```
sql-injection-lab/
├── setup.sql               # Database creation script
├── db.php                  # PDO connection + reset helper
├── index.php               # Dashboard with all lab cards
├── login_vulnerable.php    # Lab 1 - Auth bypass
├── read_vulnerable.php     # Lab 2 - OR 1=1 + UNION SELECT
├── update_vulnerable.php   # Lab 3 - Privilege escalation
├── delete_vulnerable.php   # Lab 4 - DELETE injection
├── blind_vulnerable.php    # Lab 5 - Blind boolean-based SQLi
├── login_secure.php        # Lab 6 - Prepared statements (secure)
├── LAB_GUIDE.md            # Step-by-step demo instructions
└── .gitignore
```

---

## Getting Started

### Prerequisites

- [XAMPP](https://www.apachefriends.org/) (or any local PHP + MySQL setup)
- PHP 7.4+ with PDO extension
- MySQL 5.7+

### Installation

1. **Clone the repository**

   ```bash
   git clone https://github.com/YOUR_USERNAME/sql-injection-lab.git
   cd sql-injection-lab
   ```

2. **Start MySQL** via XAMPP Control Panel (or your preferred method).

3. **Create the database**

   ```bash
   mysql -u root -p < setup.sql
   ```

4. **Configure credentials** - open `db.php` and set your MySQL password:

   ```php
   define('DB_PASS', 'your_password_here');
   ```

5. **Start the PHP development server**

   ```bash
   php -S localhost:8000
   ```

6. **Open** [http://localhost:8000](http://localhost:8000) in your browser.

---

## How Each Attack Works

### 1. Authentication Bypass

The login query uses string concatenation:

```php
$sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
```

Payload: `admin' --` bypasses the password check by commenting out the rest of the query.

### 2. Data Extraction (OR 1=1)

Payload: `' OR '1'='1` makes the WHERE clause always true, dumping every row in the table.

### 3. UNION Injection

Payload: `' UNION SELECT id, username, password, role FROM users --` appends a second query to extract any column from any table.

### 4. Privilege Escalation (UPDATE)

Payload in the password field: `hacked', role='admin' WHERE username='user1' --` injects into the SET clause to modify the user's role.

### 5. Delete Injection

Payload: `' OR '1'='1` in a DELETE query matches all rows, wiping the entire table.

### 6. Blind SQL Injection

The page only returns TRUE/FALSE. Payload: `admin' AND SUBSTRING(password,1,1)='a' --` extracts data one character at a time by observing the boolean response.

---

## The Fix: Prepared Statements

```php
// VULNERABLE (string concatenation)
$sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
$pdo->query($sql);

// SECURE (prepared statement)
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
$stmt->execute([$username, $password]);
```

Prepared statements separate SQL **code** from **data**. The database compiles the query structure first, then receives user values as literal data that can never alter the query logic.

---

## Key Takeaways

| Vulnerable Pattern         | Secure Alternative            |
|----------------------------|-------------------------------|
| String concatenation       | Prepared statements (PDO)     |
| Plaintext passwords        | bcrypt / Argon2 hashing       |
| Root database user in app  | Least-privilege DB accounts   |
| Verbose SQL error messages | Generic user-facing errors    |

---

## Disclaimer

This project is built **strictly for educational purposes**. It is part of the Securite des Applications coursework at EMSI. All demonstrations are designed to run on `localhost` only.

**Never** use these techniques against systems you do not own or have explicit authorization to test.

---

## Author

**Mohamed Dani** - EMSI

---

## License

This project is open source and available under the [MIT License](LICENSE).
