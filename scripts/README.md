# Scripts

## Commit and push (from Cursor or any terminal)

**Use this when commit/push from Chat fails** (permission denied on `.git` or no credentials).

1. Open **Terminal** in Cursor: `` Ctrl+` `` (backtick) or **View → Terminal**.
2. From project root, run:

```cmd
scripts\commit-and-push.cmd "Your commit message here"
```

Or with default message "Update":

```cmd
scripts\commit-and-push.cmd
```

This runs with your user account and has access to `.git` and your GitHub credentials.
