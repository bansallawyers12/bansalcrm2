# How to Add Follow-Up for Leads and Clients

## Checklist sheet (first-stage / follow-up sheet)

The **Checklist** sheet (Sheets → Checklist) is **separate from the follow-up system**. It shows applications that are in a **very early stage** (first-stage), with or without a follow-up:

- Application **stage** is one of the configured “early stages” (e.g. New, Inquiry, Application received). Configure in **config/sheets.php** → `checklist_early_stages`.
- Application is not discontinued; Checklist Status is **Active** or **Hold** (not Convert to client or Discontinue).

**New applications** get default Status **Active** and appear on Checklist when their stage is in the early-stages list. The last column **Status** lets you move rows:
- **Active** – stays on Checklist (normal order).
- **Hold** – stays on Checklist but sorts to the bottom.
- **Convert to client** – row leaves Checklist and appears on **Ongoing** sheet.
- **Discontinue** – row leaves Checklist and appears on **Discontinue** sheet; application is marked discontinued.

---

## Adding follow-up for **Leads**

### Where

- **Leads** → open a lead → **Notes / Follow-up** section on the lead detail page.
- From the lead list you can add/edit follow-ups via the follow-up modal.

### How (in the app)

1. Go to **Leads** and open a lead (or use the lead list).
2. Use **Add follow-up** / **Reminder** (or edit an existing follow-up).
3. Set **Subject**, **Description**, **Follow-up date & time**, and **Note type** (e.g. “Follow up”).
4. Save. The lead’s status can change to “Follow up” (status 15) when a follow-up type is set.

### Technical

- **Model:** `App\Models\Followup` (table `followups`).
- **Controller:** `App\Http\Controllers\Admin\FollowupController`.
- **Routes:**
  - `POST /followup/store` – create follow-up
  - `POST /followup/update` – update follow-up
  - `GET /followup/list?leadid=...` – list follow-ups for one lead
- **Fields:** `lead_id`, `user_id`, `note`, `subject`, `followup_type`, `followup_date`, `rem_cat`.
- **Filter leads by follow-up date:** `/leads?followupdate=YYYY-MM-DD`.
- **“Today’s follow-ups”** count/link appears on the **User view** and in the leads index (assignee + status 15 + follow-up date = today).

---

## Adding follow-up for **Clients**

### Where

- **Client detail** page → **Actions** / **Assign action** (or similar).
- **Action** module: **Action** → Assigned to me / By me / etc. – create or edit tasks that have a follow-up date.
- **Reports** → **Action calendar** – shows client follow-ups (Notes with `followup_date`) on a calendar.

### How (in the app)

1. Open a **Client** (Clients → select client).
2. Use **Assign action** / **Add action** (or go to **Action** and create a task linked to the client).
3. Fill **Subject**, **Description**, **Assignee**, and **Follow-up date/time** (`followup_datetime`).
4. Save. The action is stored as a **Note** with `followup_date` set and `folloup` = 1, `type` = `client`, `status` = 0.

Client follow-ups are used for the **Action** list and **Action calendar**; the **Checklist** sheet no longer depends on follow-ups (it shows by early stage + Status).

### Technical

- **Model:** `App\Models\Note` (table `notes`) – client actions/tasks with follow-up.
- **Controller:** `App\Http\Controllers\Admin\Client\ClientActionController` (and `ActionController` for the Action module).
- **Routes (examples):**
  - `POST /clients/action/store` – create client action (with optional `followup_datetime`)
  - `POST /clients/updateaction/store` – update action
  - Action list/calendar use the same `Note` records.
- **Fields:** `client_id`, `user_id`, `assigned_to`, `title`, `description`, `followup_date`, `folloup` (1 = is follow-up), `status` (0 = open, 1 = completed), `type` = `client`, `task_group`.
- **Activity log:** Creating/updating an action can also create/update `ActivitiesLog` and `Notification` for the assignee.

---

## Summary

|               | **Leads**                          | **Clients**                                      |
|---------------|------------------------------------|--------------------------------------------------|
| **Storage**   | `followups` table (Followup model) | `notes` table (Note model)                       |
| **Add from**  | Lead detail → follow-up / notes    | Client detail → Assign action, or Action module  |
| **Date field**| `followup_date`                    | `followup_date` (and optional `followup_datetime` in request) |
| **Checklist** | —                                  | Checklist sheet shows apps in early stages (config: `checklist_early_stages`); Status column moves rows to Ongoing / Discontinue or Hold at bottom. |

The **Checklist** sheet is independent of follow-ups: applications appear when their **stage** is in the early-stages list. Adjust **config/sheets.php** → `checklist_early_stages` to match your workflow’s first stage names (e.g. New, Inquiry, Application received).
