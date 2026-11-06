# TaskPad PHP

A minimal task tracker built with plain PHP (no framework, no database). Tasks are saved in a flat JSON file.

**Group members:** [Your name], [Partner name]   |   **Course:** [Course name and number]   |   **Instructor:** [Name]

## Features
- **Create** a task: title (required), description, priority (Low / Medium / High), due date (YYYY-MM-DD, optional)
- **List and filter** with `?q=search&priority=High`; shows counts, priority and status badges, and empty / "no matches" states
- **Complete** and **Delete** tasks with POST buttons, then redirect back with a flash message
- **Validation** on the server; errors are shown next to each field and the user's entries are kept
- **Post/Redirect/Get** after saving, so refreshing never submits twice
- **Extra:** read-only JSON view at `export.php` (shows a JSON `Content-Type`)

## Requirements
- PHP 7.4 or newer (8.x recommended). Nothing else to install.
- Check with `php -v`.

## How to run
1. Download or clone this repository and open a terminal in the project folder.
2. Start PHP's built-in server:
   ```
   php -S localhost:8000 -t public
   ```
3. Open http://localhost:8000 in your browser.
4. Optional sample data for screenshots: `php tests/seed_sample_data.php`
5. Run the automated checks: `php tests/run_tests.php`

The file `data/tasks.json` is created automatically on the first save. Delete it to start with an empty list.

## Project structure
```
taskpad-php/
  public/            web root
    index.php        list, filter, counts, flash messages (GET)
    create.php       form (GET) and handler (POST)
    actions.php      complete / delete handlers (POST only)
    export.php       JSON view of the tasks (GET)
    assets/style.css responsive, accessible styles
  src/
    storage.php      Task and TaskRepository (load/save JSON, filter, counts)
    validation.php   Validator (trim, length, priority whitelist, date check)
    csrf.php         CSRF token create / verify
    flash.php        session flash messages
    helpers.php      escaping, redirect, URL helpers
    layout.php       shared page header and footer
    bootstrap.php    session, security headers, includes
  data/              tasks.json is created here
  docs/              UML diagrams, screenshots, group evaluation, checklists
  tests/             run_tests.php and seed_sample_data.php
```

## Security and input handling
- Inputs are read from `$_POST` / `$_GET`, trimmed, length-checked and validated on the server (priority whitelist, date regex plus `checkdate`).
- Everything echoed into HTML is escaped with `htmlspecialchars` (XSS protection).
- Changing data (create, complete, delete) only works with POST; `actions.php` answers GET with HTTP 405.
- Every POST form carries a CSRF token that is verified on the server.
- Redirects after actions are rebuilt from whitelisted values, so they cannot be pointed at another site.
- Writes use file locking so two requests cannot corrupt `tasks.json`.
- Content types are set explicitly (`text/html; charset=UTF-8`, `application/json`).

## Documentation and evidence
| What | Where |
|------|-------|
| Use Case diagram | `docs/uml-usecase.pdf` (source: `docs/uml/usecase.puml`) |
| Class diagram | `docs/uml-class.pdf` (source: `docs/uml/class.puml`) |
| Activity diagram (optional) | `docs/uml-activity.pdf` (source: `docs/uml/activity.puml`) |
| Screenshots (desktop, tablet, mobile) | `docs/screenshots/` (list of files in `docs/screenshots/README.md`) |
| Group evaluation | `docs/GroupEval.docx` |
| Manual testing checklist | `docs/testing-checklist.md` |

To regenerate the diagrams from the `.puml` files, use PlantUML (https://plantuml.com) or paste the text into https://www.plantuml.com/plantuml and export as PDF.

## Requirements covered
| Requirement | Where |
|-------------|-------|
| Create task with validation, inline errors, kept inputs | `public/create.php`, `src/validation.php` |
| PRG redirect after saving | `public/create.php` (HTTP 303) |
| List, text + priority filters, counts, empty states | `public/index.php`, `src/storage.php` |
| Complete / Delete via POST, flash message, redirect | `public/actions.php`, `src/flash.php` |
| JSON persistence in `/data/tasks.json` | `src/storage.php` |
| UML Use Case + Class (+ Activity) | `docs/` |
