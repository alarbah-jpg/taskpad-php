# Manual testing checklist

Run `php -S localhost:8000 -t public`, open http://localhost:8000 and tick each item.
Automated checks: `php tests/run_tests.php` (should end with "0 failed").

## Create (create.php)
- [ ] Empty title shows "Title is required." and keeps the other fields filled in
- [ ] Title with only spaces is rejected
- [ ] Date `2026-02-30` is rejected; `2026-12-31` is accepted; blank date is accepted
- [ ] Valid task redirects to the list and shows a green "was added" message
- [ ] Refreshing the list page does NOT add the task again (PRG)
- [ ] Title `<script>alert(1)</script>` appears as plain text, no pop-up

## List and filter (index.php)
- [ ] Empty list shows the "No tasks yet" state
- [ ] Counts (Total / Open / Completed) are correct
- [ ] `?q=report` matches title or description, upper and lower case
- [ ] `?priority=High` shows only High tasks; `?q=report&priority=High` combines both
- [ ] A search with no result shows "No matching tasks" and a Clear link
- [ ] `?priority=Banana` and `?q[]=x` do not break the page

## Complete and delete (actions.php)
- [ ] Complete shows the "Completed" badge and a green message; the filter stays applied
- [ ] Delete asks for confirmation, then removes the task and shows a message
- [ ] Opening http://localhost:8000/actions.php directly shows "405 Method Not Allowed"

## Responsive and accessibility
- [ ] Layout works at 375px (mobile), 768px (tablet) and 1280px (desktop) wide
- [ ] Every control can be reached with the Tab key and has a visible focus outline
- [ ] Priority and status are shown with words, not colour alone

## Extras
- [ ] http://localhost:8000/export.php returns JSON (`Content-Type: application/json`)
