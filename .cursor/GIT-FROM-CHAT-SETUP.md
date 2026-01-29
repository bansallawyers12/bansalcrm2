# Fix: Git commit/push from chat (Permission denied on .git)

The agent runs in a restricted terminal. On Windows this can block writing to `.git` (e.g. `index.lock`). Enable Cursor settings so git works from chat.

## 1. Allow Git writes (recommended)

1. Open **Cursor Settings**: `Ctrl+,` or **File → Preferences → Settings**.
2. Search for **Allow Git Writes** or open **Cursor Settings → Agents → Auto-Run**.
3. Turn **ON**: **Allow Git Writes Without Approval**.

This lets the agent run `git commit`, `git push`, etc. without approval and with access to `.git`.

## 2. Allow network for push

Push needs network access:

1. In **Cursor Settings → Agents → Auto-Run**.
2. Turn **ON**: **Auto-Run Network Access** (or the option that allows network for sandboxed commands).

## 3. If it still fails: add git to the allowlist

When a git command fails, Cursor may show **Run** / **Add to allowlist**:

1. Choose **Add to allowlist** so future git commands run outside the sandbox.
2. Or in **Cursor Settings → Agents → Auto-Run → Command Allowlist**, add:
   - `git`
   - Or the full script path, e.g. `scripts\commit-and-push.cmd`

## 4. Fallback: run the task in your terminal

If you prefer not to change Cursor settings:

- **Ctrl+Shift+P** → **Tasks: Run Task** → **Git: Commit and Push** (or **Git: Pull**, **Git: Push**, **Git: Sync**).

Your terminal has full access to `.git` and credentials.
