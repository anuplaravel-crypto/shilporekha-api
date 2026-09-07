---
name: commit-push
description: Writes a Conventional Commits-formatted commit message from the actual staged/unstaged git changes and pushes to the current branch's remote. Use whenever the user says "commit and push", "commit this", "push this up", "commit these changes", or similar in ANY git repository — not tied to one project. Infers the commit type (feat/fix/docs/style/refactor/perf/test/build/ci/chore/revert) and scope directly from the diff content rather than writing a generic message like "update files".
---

# Commit & push with a real Conventional Commit message

The point of this skill is that the commit message should read like a
human who actually looked at the diff wrote it — specific about what
changed and why — never a placeholder like "update code" or "fix
stuff". Everything below exists in service of that.

## 1. Look at what actually changed

- `git status` — working tree state.
- `git diff` (unstaged) and `git diff --staged` (staged) — the real
  content changes, not just filenames. The type and scope come from
  what the code *does*, not which files happen to be touched.
- `git log --oneline -5` — some repos already lean toward a scope
  convention worth matching.

## 2. Stage what belongs in this commit

If nothing is staged yet, stage the relevant changes. Use judgment —
`git add -A` is fine when everything in `git status` clearly belongs
together, but leave out obvious scratch/debug output if it's sitting
alongside real changes. If the user pointed at specific files, stage
only those.

## 3. Settle the branch before committing

- `git branch --show-current`, and check whether the repo has any
  commits yet (`git rev-parse HEAD` fails on a brand-new repo).
- **Brand-new repo, first commit ever**: committing straight to
  `main`/`master` is normal — go ahead.
- **Existing repo, currently on `main`/`master`**: create a new branch
  first (short, descriptive, e.g. `feat/order-brief-endpoint`) and
  switch to it before committing. This keeps the default branch clean,
  which is what a real team would expect.
- **Already on a feature branch**: just commit there.

## 4. Write the message — Conventional Commits

```
<type>(<scope>): <short imperative summary>

<body — only if the summary alone doesn't explain the "why">

<BREAKING CHANGE: ... — only if applicable>
```

- **type** — match the diff, don't default to `chore` out of laziness:
  `feat` (new capability), `fix` (bug fix), `docs`, `style` (formatting
  only, no logic change), `refactor` (restructuring, same behavior),
  `perf`, `test`, `build` (build system/deps), `ci`, `chore`
  (maintenance/config), `revert`.
- **scope** — the module/area touched, inferred from the changed
  paths (`app/Models/Service.php` → `models`; `src/router/index.js` →
  `router`). Leave it off entirely for a broad, cross-cutting change
  rather than forcing a scope that doesn't fit.
- **summary** — imperative ("add", not "added"), lowercase after the
  colon, no trailing period, aim under ~72 characters.
- **body** — add one only when it earns its place: a non-obvious fix,
  a breaking change, a design decision worth recording. A trivial
  commit with no body is correct, not incomplete.
- If the diff doesn't make a specific message obvious, look closer at
  it rather than falling back to something vague — that's the whole
  point of this skill.

## 5. Attribution trailer

If the current session has a configured commit attribution line (for
example `Co-Authored-By: Claude Sonnet 5 <noreply@anthropic.com>`),
append it as the last line, separated from the body by a blank line.
Don't invent one if none is configured.

## 6. Commit

Always a new commit. Never `--amend` unless the user explicitly asks
to amend. Never `--no-verify` — if a hook fails, fix what it's
flagging rather than bypassing it.

## 7. Push

- No upstream yet → `git push -u origin <branch>`.
- Otherwise → plain `git push`.
- Never force-push (`--force` / `--force-with-lease`) unless the user
  explicitly asks for it and clearly understands what that does.

## 8. Report back

Short commit hash, the branch it landed on, and the push result. If
the remote is GitHub and the branch isn't the default branch, include
the compare/PR link so the user's next step is one click away:
`https://github.com/<owner>/<repo>/compare/<branch>?expand=1`

## Example

Diff adds a `store()` method to `OrderController` that validates and
saves a brief submission:

```
feat(orders): add endpoint to submit a project brief

Validates name, email, service, and description, then creates
the Order record with its attached style-reference files.
```

Not this:

```
update controller
```
