# Submission checklist

Go through every item before you submit on Canvas. Tick a box only after you have checked it yourself.

## 1. Repository and access
- [ ] The repository opens in a private/incognito browser window (public), **or** the instructor has been added under *Settings, then Collaborators*
- [ ] The partner's collaborator invite has been accepted
- [ ] The front page shows `public`, `src`, `data`, `docs`, `tests`, `README.md` and `.gitignore`
- [ ] `data/` contains only `README.txt` (no `tasks.json`)
- [ ] No unrelated or temporary files (for example `.zip` files, `.vscode`)

## 2. The application
- [ ] Download the repo with *Code, then Download ZIP*, extract it, run `php -S localhost:8000 -t public`, and the app loads
- [ ] `php tests/run_tests.php` ends with `29 passed, 0 failed`
- [ ] Create, list, filter, complete and delete all work
- [ ] Saving redirects to the list and refreshing does not add a duplicate
- [ ] Error messages appear next to the fields and the entries are kept
- [ ] Tasks are still there after stopping and restarting the server

## 3. UML diagrams
- [ ] `docs/uml-usecase.pdf`, `docs/uml-class.pdf` and `docs/uml-activity.pdf` are in the repo and open correctly
- [ ] `docs/uml/usecase.puml`, `class.puml` and `activity.puml` are in the repo
- [ ] Member names appear on the diagrams (the instructions ask for names on submitted documents)

## 4. Screenshots
- [ ] `docs/screenshots/` holds all 24 images: 8 states at desktop, tablet and mobile sizes
- [ ] File names match `docs/screenshots/README.md` exactly (lowercase, hyphens, `.png`)
- [ ] The screenshot gallery in `docs/screenshots/README.md` displays every image
- [ ] Each image shows the right state and size (for example, mobile images look like a phone layout)

## 5. README and documentation
- [ ] `README.md` has both member names
- [ ] The run instructions work as written
- [ ] The links to the screenshots, UML files, testing checklist and group evaluation all open
- [ ] No placeholders such as `[Your name]` or `[Course name]` remain
- [ ] `docs/testing-checklist.md` is complete: no `☐` boxes left, and the summary line is updated

## 6. Group evaluation
- [ ] `docs/GroupEval.docx` is the filled-in version, with scores for both members and no yellow highlighting
- [ ] Both names are in the header row and the comments match the team contributions in the README
- [ ] You have confirmed with the instructor whether the form belongs in the repo (the form says it is confidential)

## 7. Names
- [ ] Both full names are in the README, the group evaluation, the UML diagrams and the Canvas comment

## 8. Canvas
- [ ] The repository URL is pasted into the submission (for example `https://github.com/<user>/taskpad-php`)
- [ ] The submission comment lists every member's full name
- [ ] You have submitted before the due date and saved the confirmation

## Rubric check

| Rubric item | Points | Where the evidence is |
|---|---|---|
| Functionality: create, list, filter, complete, delete, persistence, redirect after saving, validation messages | 20 | `public/`, `src/`, `data/`; `docs/testing-checklist.md`; `tests/run_tests.php` |
| UML | 5 | `docs/uml-*.pdf` and `docs/uml/*.puml` |
| UI/UX and responsiveness: clarity, empty, error and success states, mobile-first layout | 5 | `public/assets/style.css`; `docs/screenshots/` |

Check the Canvas assignment page for any rubric items, due date or extra rules not listed here.

## After submitting
- [ ] Open the submitted link once from Canvas to be sure it works
- [ ] Avoid changing the repository after the deadline unless you have to, because the grader sees the latest version
