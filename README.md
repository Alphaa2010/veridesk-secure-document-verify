# VeriDesk — Secure Document Verification Portal

VeriDesk is a local PHP and MySQL portfolio application for demonstrating document submission, manual review, candidate access control, notifications, and audit records. It does not independently verify identity documents, contact universities, search criminal records, or authenticate official certificates. Decisions shown in the portal are made manually by an administrator. Use fictional information only.

## Features

- Candidate registration, sign-in, sign-out, password hashing, role checks, and session renewal.
- Candidate application dashboard, reference number, progress, upload workflow, review status, and administrator remarks.
- Four document categories, secure PDF/JPG/PNG upload validation, private storage, and authenticated viewing/downloading.
- Administrator dashboard with database-backed metrics and charts, application search/status filter, application review, and activity log.
- Per-document decisions, required reasons for rejection/resubmission, automatic application status calculation, notifications, and audit events.
- PDO prepared queries, CSRF tokens, escaped output, secure session cookies, and a private upload directory.

## Stack

PHP 8.1+, MySQL 8 or MariaDB, Apache (XAMPP), PDO MySQL, HTML/CSS/JavaScript. Chart.js is loaded from jsDelivr on the administrator dashboard.

## XAMPP installation (Windows)

1. Install XAMPP with Apache, MySQL, and PHP 8.1 or newer. Start Apache and MySQL in the XAMPP Control Panel.
2. Copy this folder into `C:\xampp\htdocs\veridesk` (or configure an Apache virtual host with `public` as its document root).
3. Open `http://localhost/phpmyadmin`, create/select the database by importing `database/veridesk.sql`. The SQL script creates and selects the `veridesk` database itself.
4. Copy `config/config.example.php` to `config/local.php`. Change the database host, name, username, password, and upload location if needed. `config/local.php` is ignored by Git. With a default local XAMPP install, the example's root account with an empty password is often used; set a local MySQL password if yours differs.
5. Ensure `storage/uploads` exists and is writable by the Apache account. It is outside the public document root. Do not move it under `public`.
6. Set the local demo passwords in PowerShell, then run the seeder from the project folder: `$env:VERIDESK_ADMIN_PASSWORD='your-local-admin-password'` and `$env:VERIDESK_CANDIDATE_PASSWORD='your-local-candidate-password'`, followed by `C:\xampp\php\php.exe database\seed.php`. The script can be rerun safely for the seeded accounts and their documents. Both passwords must be at least 12 characters.
7. Browse to `http://localhost/veridesk/public/`. For a cleaner URL, set the virtual host document root directly to `C:\xampp\htdocs\veridesk\public`.

### Demo accounts

The local-only `README.local.md` contains suggested local demo credentials and is excluded from Git. Set those values in `VERIDESK_ADMIN_PASSWORD` and `VERIDESK_CANDIDATE_PASSWORD` before running the seeder. These accounts should only be used in a local demo environment.

## Folder structure

```text
app/                  Shared PDO and security/workflow helpers
config/               Bootstrap and ignored local configuration
database/             Importable schema and idempotent demo seeder
public/               Apache document root, front controller, CSS and JS
storage/uploads/      Private uploaded documents (ignored by Git)
storage/logs/         Local log location
```

## Security and limitations

Uploads are limited to 8 MiB and checked by extension and server-detected MIME type. Stored filenames are random; original names are kept only as metadata. Downloads go through an authenticated endpoint that checks candidate ownership. Administrator review and all state-changing forms require a CSRF token. Passwords use PHP's password hashing API. Error details are logged server-side for PDO failures and not shown to users.

For deployment beyond a local portfolio demo, use HTTPS, a least-privilege database account, a private configuration file outside the document root, rate limiting, backups, malware scanning, retention controls, stronger operational monitoring, and a formal privacy/security review. This demonstration has no email delivery and no external identity checks. Chart.js currently requires network access to jsDelivr. Seeded document rows are deliberately metadata-only placeholders: they contain no actual identity documents and have no downloadable sample file.

## Test checklist

After installation, use two browser profiles (candidate and administrator) and check:

1. Register a new candidate, then sign out and sign back in. Try opening `?page=admin` as a candidate; it must return access denied.
2. Sign in with the demo administrator and open Applications. Search for a candidate name/reference and filter by status.
3. As a candidate, upload a valid small PDF/JPG/PNG in each category. Try a renamed executable, invalid MIME type, and a file over 8 MiB; each must be rejected.
4. As another candidate, try the first candidate's `?page=file&id=...` URL; the file must not be exposed.
5. As administrator, approve all four documents; application status should become Completed. Reject or request resubmission with a reason; the application should become Action Required and the candidate should see the remark and replacement control.
6. Replace the requested document and check candidate/admin notifications and the Activity log.
7. Compare dashboard figures to the applications/documents in the database.

This workspace did not have PHP, MySQL, or XAMPP available during implementation, so this checklist has not been executed here. Run it after configuring a local XAMPP instance.
