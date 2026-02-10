# Time Tracker

A personal time tracking tool with a PHP backend and Tailwind CSS frontend.

## Setup

1.  **Database**:
    -   Ensure the MySQL database `d021a4e0` exists.
    -   Import the schema if not already present (provided in the prompt).
    -   Verify credentials in `config/db.php`.

2.  **Deployment**:
    -   Upload `public/` and `src/`/`config/` to your server.
    -   Point your web server document root to the `public/` directory.

3.  **Basic Authentication**:
    The project is configured to use standard Basic Auth via `.htaccess`.
    
    1.  Create a `.htpasswd` file outside your web root (e.g. `/var/www/.htpasswd`).
    2.  Generate the password hash. You can use an online generator or the command line:
        ```bash
        htpasswd -c /path/to/.htpasswd d021a4e0
        ```
    3.  Edit `public/.htaccess` (created below) and update the `AuthUserFile` path to match your server's absolute path to the `.htpasswd` file.

## Features
-   **Detailed View**: Raw entries with Gap/Overlap detection (Red background).
-   **Consecutive View**: Aggregates adjacent tasks with the same ID.
-   **Grouped View**: Totals for the day per Task ID.
-   **Type Selector**: Switch between "Real Time" and "Tracking Time" (Reported).
-   **Visuals**:
    -   Transferred status (transfer=0) dims the row.
    -   PAUSE tasks are dimmed/highlighted.
    -   Internal/Jira transfer status shown with color codes.
