# Time Tracker

A personal time tracking tool with a PHP backend and Tailwind CSS frontend.

## Setup

1.  **Database**:
    -   Create a new MySQL database for the application.
    -   Import the schema (provided in the SQL dump).
    -   Copy `config/db.example.php` to `config/db.php`.
    -   Update credentials in `config/db.php` to match your new database.

2.  **Deployment**:
    -   Upload the project files to your server.
    -   Point your web server document root to the `public/` directory.

3.  **Security**:
    -   **Important**: This project does not currently include built-in authentication routing. It is highly recommended that you implement standard Basic Authentication (or another authentication method) at the web server level before deploying to a production environment.
## Features
-   **Detailed View**: Raw entries with Gap/Overlap detection (Red background).
-   **Consecutive View**: Aggregates adjacent tasks with the same ID.
-   **Grouped View**: Totals for the day per Task ID.
-   **Type Selector**: Switch between "Real Time" and "Tracking Time" (Reported).
    - This is currentöy not implemented yet. So the switch is present in the UI, the corresponding fields are defined in the database schema, but apart from the switch the UI does not reflect the type selection yet.
-   **Visuals**:
    -   Transferred status (transfer=0) dims the row.
    -   PAUSE tasks are dimmed/highlighted.
    -   Internal/Jira transfer status shown with color codes.
