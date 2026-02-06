# How to Add Follow-Up for Leads and Clients

## Checklist sheet (follow-up only)

The **Checklist** sheet (Sheets → Checklist) now shows **only applications whose client has a pending follow-up**:

- Client has at least one **Note** (action) with:
  - `followup_date` set
  - `status` = 0 (open/pending)
  - `type` = `client`
- Application is not discontinued and stage is not COE Issued, Enrolled, or COE Cancelled.

So the Checklist is a “follow-up only” view: add a follow-up for a client (see below) and that client’s applications will appear on the Checklist until the follow-up is completed or the date is cleared.

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

When that client has at least one such **pending** follow-up (open, with a date), their applications will show on the **Checklist** sheet.

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
| **Checklist** | —                                  | Checklist sheet shows apps whose client has a pending Note follow-up |

To have a client’s applications appear on **Checklist**, add at least one **client action (Note)** with a **follow-up date** and leave it **open** (`status` = 0). Complete or remove the follow-up and they will drop off the Checklist (when no other pending follow-up exists for that client).
