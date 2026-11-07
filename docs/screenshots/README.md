# Screenshots

Screenshots of TaskPad PHP in every state the assignment asks for, at three screen sizes. Click any thumbnail to open the full image.

## Coverage

| Screen size | How it was captured | Images |
|---|---|---|
| **Desktop** | Full browser window on a laptop | 8 |
| **Tablet** | Chrome/Edge DevTools device toolbar, iPad Mini (768 px wide) | 8 (plus 1 extra) |
| **Mobile** | Chrome/Edge DevTools device toolbar, phone-sized screen | 8 |

That is 8 states at 3 sizes: **24 required images**, plus one extra (`nomatch-tablet.png`).

**File names** follow the pattern `<state>-<device>.png`, for example `create-error-mobile.png`.

**Sample data.** Desktop screenshots use tasks added by hand. Tablet and mobile screenshots use the sample data from `php tests/seed_sample_data.php`, so the task titles differ. For that reason the filtered list uses `?q=report&priority=High` on desktop and `?q=assignment&priority=High` on tablet and mobile.

**Who took them.** Laptop and tablet screenshots: Mohamed Alarbah. Mobile screenshots: Mohamed Abdulla.

---

## 1. Create form: error state
Saving with an empty title. The page shows an error summary, "Title is required." next to the field, a red border on the field, and the text typed in the description is kept.

| Desktop | Tablet | Mobile |
|---|---|---|
| <a href="create-error-desktop.png"><img src="create-error-desktop.png" width="240" alt="Create form with an error, desktop"></a> | <a href="create-error-tablet.png"><img src="create-error-tablet.png" width="240" alt="Create form with an error, tablet"></a> | <a href="create-error-mobile.png"><img src="create-error-mobile.png" width="240" alt="Create form with an error, mobile"></a> |

## 2. Create form: success state
After saving a valid task the app redirects to the list and shows a green "was added" message (Post/Redirect/Get).

| Desktop | Tablet | Mobile |
|---|---|---|
| <a href="create-success-desktop.png"><img src="create-success-desktop.png" width="240" alt="List with a success message, desktop"></a> | <a href="create-success-tablet.png"><img src="create-success-tablet.png" width="240" alt="List with a success message, tablet"></a> | <a href="create-success-mobile.png"><img src="create-success-mobile.png" width="240" alt="List with a success message, mobile"></a> |

## 3. List view without filters
All tasks with the Total, Open and Completed counts, priority and status badges, due dates, and an "Overdue" badge on a past-due open task.

| Desktop | Tablet | Mobile |
|---|---|---|
| <a href="list-nofilter-desktop.png"><img src="list-nofilter-desktop.png" width="240" alt="Task list without filters, desktop"></a> | <a href="list-nofilter-tablet.png"><img src="list-nofilter-tablet.png" width="240" alt="Task list without filters, tablet"></a> | <a href="list-nofilter-mobile.png"><img src="list-nofilter-mobile.png" width="240" alt="Task list without filters, mobile"></a> |

## 4. List view with filters
A text search combined with a priority filter. The page shows "Showing X of Y tasks" and a Clear button.

| Desktop | Tablet | Mobile |
|---|---|---|
| <a href="list-filtered-desktop.png"><img src="list-filtered-desktop.png" width="240" alt="Filtered task list, desktop"></a> | <a href="list-filtered-tablet.png"><img src="list-filtered-tablet.png" width="240" alt="Filtered task list, tablet"></a> | <a href="list-filtered-mobile.png"><img src="list-filtered-mobile.png" width="240" alt="Filtered task list, mobile"></a> |

## 5. Complete: before
The list before a task is marked complete. The task is open and has a Complete button.

| Desktop | Tablet | Mobile |
|---|---|---|
| <a href="complete-before-desktop.png"><img src="complete-before-desktop.png" width="240" alt="List before completing a task, desktop"></a> | <a href="complete-before-tablet.png"><img src="complete-before-tablet.png" width="240" alt="List before completing a task, tablet"></a> | <a href="complete-before-mobile.png"><img src="complete-before-mobile.png" width="240" alt="List before completing a task, mobile"></a> |

## 6. Complete: after
The same task after clicking Complete. It now shows a grey "Completed" badge and strikethrough title, the counts have changed, and a green message confirms the action.

| Desktop | Tablet | Mobile |
|---|---|---|
| <a href="complete-after-desktop.png"><img src="complete-after-desktop.png" width="240" alt="List after completing a task, desktop"></a> | <a href="complete-after-tablet.png"><img src="complete-after-tablet.png" width="240" alt="List after completing a task, tablet"></a> | <a href="complete-after-mobile.png"><img src="complete-after-mobile.png" width="240" alt="List after completing a task, mobile"></a> |

## 7. Delete: before
The list before a task is deleted. Each task has a Delete button, which asks for confirmation first.

| Desktop | Tablet | Mobile |
|---|---|---|
| <a href="delete-before-desktop.png"><img src="delete-before-desktop.png" width="240" alt="List before deleting a task, desktop"></a> | <a href="delete-before-tablet.png"><img src="delete-before-tablet.png" width="240" alt="List before deleting a task, tablet"></a> | <a href="delete-before-mobile.png"><img src="delete-before-mobile.png" width="240" alt="List before deleting a task, mobile"></a> |

## 8. Delete: after
After confirming, the task is gone, the counts are lower, and a green "was deleted" message is shown.

| Desktop | Tablet | Mobile |
|---|---|---|
| <a href="delete-after-desktop.png"><img src="delete-after-desktop.png" width="240" alt="List after deleting a task, desktop"></a> | <a href="delete-after-tablet.png"><img src="delete-after-tablet.png" width="240" alt="List after deleting a task, tablet"></a> | <a href="delete-after-mobile.png"><img src="delete-after-mobile.png" width="240" alt="List after deleting a task, mobile"></a> |

## Extra: no matching tasks
A search with no results shows a "No matching tasks" message and a Clear filters button.

<a href="nomatch-tablet.png"><img src="nomatch-tablet.png" width="240" alt="No matching tasks state, tablet"></a>

---

Back to the [project README](../../README.md).
