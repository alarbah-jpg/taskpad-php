# Testing checklist and results

| | |
|---|---|
| **Application** | TaskPad PHP |
| **Tested by** | Mohamed Alarbah and Mohamed Abdulla |
| **Date** | November 2026 |
| **PHP version** | 8.5.9 (Windows) |
| **Browser** | Chrome or Edge, with the DevTools device toolbar for tablet and mobile sizes |
| **Summary** | 24 checks: 15 passed with evidence, 9 still to run (see the rows marked ☐) |

## How to run the tests

1. Start the app: `php -S localhost:8000 -t public`, then open <http://localhost:8000>.
2. Work through the tables below. After each check, change `☐` to **Pass** or **Fail** and note where the evidence is.
3. Run the automated checks: `php tests/run_tests.php`.

## Automated tests

| Command | Result |
|---|---|
| `php tests/run_tests.php` | **29 passed, 0 failed** |

They cover input validation (empty and space-only titles, length limits, priority whitelist, impossible dates, array input), saving and loading tasks, filtering and counts, and a corrupted data file.

## 1. Create a task (`create.php`)

| ID | What to check | Expected result | Result | Evidence |
|---|---|---|---|---|
| C1 | Save with an empty title | "Title is required." appears next to the field and the other fields stay filled in | Pass | `docs/screenshots/create-error-*.png` |
| C2 | Save with a title of only spaces | Rejected like an empty title | Pass | Automated test "whitespace-only title is rejected" |
| C3 | Save with a title over 100 characters | Rejected with a length message | Pass | Automated test "title longer than 100 characters is rejected" |
| C4 | Due date: impossible (`2026-02-30`), valid (`2026-12-31`), blank | Impossible rejected; valid and blank accepted | Pass | Automated tests for dates. The browser's date picker blocks impossible dates, so the server rule is tested automatically |
| C5 | Save a valid task | Redirects to the list and shows a green "was added" message | Pass | `docs/screenshots/create-success-*.png` |
| C6 | Press F5 on the list right after saving | The task is not added twice (Post/Redirect/Get) | ☐ | |
| C7 | Save a task titled `<script>alert(1)</script>` | Shown as plain text, with no pop-up | ☐ | Escaping function is covered by the automated test "e() escapes script tags" |

## 2. List and filter (`index.php`)

| ID | What to check | Expected result | Result | Evidence |
|---|---|---|---|---|
| L1 | Open the list with no tasks (delete `data/tasks.json` first) | "No tasks yet" state with an Add task button | ☐ | |
| L2 | Look at the counts | Total, Open and Completed match the tasks shown | Pass | `docs/screenshots/list-nofilter-*.png` |
| L3 | Search for part of a title or description, in upper and lower case | Matching tasks appear, regardless of case | Pass | `list-filtered-*.png` and automated tests "text filter is case-insensitive" and "matches description" |
| L4 | Filter by priority, and combine it with a search | Only matching tasks are shown, with "Showing X of Y" | Pass | `docs/screenshots/list-filtered-*.png` |
| L5 | Search for text that matches nothing | "No matching tasks" and a Clear link | Pass | `docs/screenshots/nomatch-tablet.png` |
| L6 | Look at an open task with a past due date | Red "Overdue" badge | Pass | `docs/screenshots/list-nofilter-*.png` |
| L7 | Open `?priority=Banana` and `?q[]=x` | The page loads normally and ignores the bad values | Pass | Automated tests for filter sanitizing |

## 3. Complete and delete (`actions.php`)

| ID | What to check | Expected result | Result | Evidence |
|---|---|---|---|---|
| A1 | Click Complete on an open task | Grey "Completed" badge and a green message | Pass | `docs/screenshots/complete-after-*.png` |
| A2 | Apply a filter, then click Complete | The same filter is still applied afterwards | ☐ | |
| A3 | Click Delete | A confirmation pop-up appears first | ☐ | |
| A4 | Confirm the delete | The task disappears and a green "was deleted" message shows | Pass | `docs/screenshots/delete-after-*.png` |
| A5 | Open `http://localhost:8000/actions.php` directly in the browser | "405 Method Not Allowed" (only POST is accepted) | ☐ | |

## 4. Responsive layout and accessibility

| ID | What to check | Expected result | Result | Evidence |
|---|---|---|---|---|
| R1 | View the app at 1280, 768 and 390 pixels wide | Content fits with no sideways scrolling and stays readable | Pass | Screenshots at all three sizes in `docs/screenshots/` |
| R2 | Press Tab repeatedly through the page | Every link, field and button is reached and has a visible focus outline | ☐ | |
| R3 | Look at priority and status labels | They use words ("Priority: High", "Completed"), not colour alone | Pass | Badges in the list screenshots |

## 5. Persistence and extras

| ID | What to check | Expected result | Result | Evidence |
|---|---|---|---|---|
| X1 | Stop the server (Ctrl+C), start it again, and reload | The tasks are still there (saved in `data/tasks.json`) | ☐ | |
| X2 | Open `http://localhost:8000/export.php` | JSON is returned, with `Content-Type: application/json` (check under DevTools, Network) | ☐ | |

## Notes

- Delete `data/tasks.json` after testing so test data is not uploaded. It is excluded by `.gitignore`.
- To try a bad date in the browser, open DevTools, change the date field's `type="date"` to `type="text"`, type `2026-02-30` and save.
