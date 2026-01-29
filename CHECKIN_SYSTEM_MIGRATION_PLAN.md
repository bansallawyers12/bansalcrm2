# Check-in System Migration Plan
## Copying from migrationmanager2 to bansalcrm2

**Date:** 2026-01-29  
**Status:** Planning - Not Applied  
**Source:** C:\xampp\htdocs\migrationmanager2  
**Target:** C:\xampp\htdocs\bansalcrm2

---

## Executive Summary

This plan outlines the steps to migrate the improved office check-in system from migrationmanager2 to bansalcrm2. The migration fixes the current `is_archived` NOT NULL constraint violation error and restores the CheckinHistory functionality that was previously removed.

**Current Problem in bansalcrm2:**
- **Error:** `SQLSTATE[23502]: Not null violation: 7 ERROR: null value in column "is_archived" of relation "checkin_logs" violates not-null constraint`
- **Cause:** `OfficeVisitController::checkin()` does not set `is_archived` when creating check-ins
- **Impact:** Office check-ins cannot be created

**Solution from migrationmanager2:**
- Sets `is_archived = 0` on check-in creation
- Includes validation, error handling, and DB transactions
- Maintains CheckinHistory for audit trail and comments
- Implements real-time notifications via Reverb/broadcasting

---

## Comparison: Current vs Target State

### bansalcrm2 (Current - Broken)

**CheckinLog Model:**
```php
protected $fillable = ['id', 'created_at', 'updated_at'];
// Missing: is_archived and other fields
```

**Controller Issues:**
- ❌ Does not set `is_archived` on create → causes NOT NULL violation
- ❌ No validation on contact, assignee, office
- ❌ No transaction wrapping
- ❌ Limited error handling
- ❌ CheckinHistory table dropped and disabled
- ❌ Uses ActivitiesLog instead (only for clients, not leads)
- ⚠️ Hardcoded `receiver_id = 22136` in `attend_session`
- ❌ No real-time notifications via broadcasting

**Notification Model:**
```php
class Notification extends Model {
    use Sortable;
}
// Missing: fillable, relationships, receiver_status, sender_status
```

### migrationmanager2 (Target - Working)

**CheckinLog Model:**
```php
protected $fillable = [
    'id', 'client_id', 'user_id', 'visit_purpose', 'office', 
    'contact_type', 'status', 'date', 'sesion_start', 'sesion_end', 
    'wait_time', 'attend_time', 'wait_type', 'is_archived', 
    'created_at', 'updated_at'
];

// Relationships
public function client() { ... }
public function assignee() { ... }
```

**Controller Features:**
- ✅ Sets `is_archived = 0` on create
- ✅ Validates contact (handles Select2 array), assignee, office, message
- ✅ Verifies contact, assignee, and office exist
- ✅ DB transaction with rollback on failure
- ✅ Comprehensive error logging
- ✅ CheckinHistory maintained for audit trail
- ✅ Broadcasts `OfficeVisitNotificationCreated` event
- ⚠️ Hardcoded `receiver_id = 36608` in `attend_session` (same issue)

**Notification Model:**
```php
protected $fillable = [
    'sender_id', 'receiver_id', 'module_id', 'url', 
    'notification_type', 'message', 'receiver_status', 'seen'
];

// Relationships
public function checkinLog() { ... }
public function sender() { ... }
public function receiver() { ... }
```

**CheckinHistory Model:**
```php
protected $fillable = [
    'id', 'subject', 'created_by', 'checkin_id', 
    'description', 'created_at', 'updated_at'
];
```

**Broadcasting Event:**
```php
class OfficeVisitNotificationCreated implements ShouldBroadcastNow
{
    public function broadcastOn(): array {
        return [new PrivateChannel("user.{$this->receiverId}")];
    }
    
    public function broadcastAs(): string {
        return 'OfficeVisitNotificationCreated';
    }
}
```

---

## Migration Tasks

### Phase 1: Database Schema & Models

#### 1.1 Restore `checkin_histories` Table

**File:** `database/migrations/2026_01_29_000001_restore_checkin_histories_table.php`

**Actions:**
- Create migration to restore `checkin_histories` table
- Schema:
  ```sql
  CREATE TABLE checkin_histories (
      id SERIAL PRIMARY KEY,
      subject VARCHAR(255) NOT NULL,
      created_by INTEGER NOT NULL,
      checkin_id INTEGER NOT NULL,
      description TEXT,
      created_at TIMESTAMP,
      updated_at TIMESTAMP,
      FOREIGN KEY (checkin_id) REFERENCES checkin_logs(id) ON DELETE CASCADE,
      FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE CASCADE
  );
  CREATE INDEX idx_checkin_histories_checkin_id ON checkin_histories(checkin_id);
  CREATE INDEX idx_checkin_histories_created_by ON checkin_histories(created_by);
  ```

**Reason:** CheckinHistory provides audit trail and comment functionality that was removed in bansalcrm2

#### 1.2 Update CheckinLog Model

**File:** `app/Models/CheckinLog.php`

**Changes:**
```php
<?php
namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;

class CheckinLog extends Model
{
    use Sortable;

    protected $fillable = [
        'id', 'client_id', 'user_id', 'visit_purpose', 'office',
        'contact_type', 'status', 'date', 'sesion_start', 'sesion_end',
        'wait_time', 'attend_time', 'wait_type', 'is_archived',
        'created_at', 'updated_at'
    ];

    public $sortable = ['id', 'created_at', 'updated_at'];

    /**
     * Get the client for this check-in
     */
    public function client()
    {
        if ($this->contact_type == 'Lead') {
            return $this->belongsTo('App\Models\Lead', 'client_id');
        } else {
            return $this->belongsTo('App\Models\Admin', 'client_id')->where('role', '7');
        }
    }

    /**
     * Get the assignee for this check-in
     */
    public function assignee()
    {
        return $this->belongsTo('App\Models\Admin', 'user_id');
    }
}
```

#### 1.3 Create CheckinHistory Model

**File:** `app/Models/CheckinHistory.php`

**Actions:**
```php
<?php
namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;

class CheckinHistory extends Model
{
    use Sortable;

    protected $fillable = [
        'id', 'subject', 'created_by', 'checkin_id',
        'description', 'created_at', 'updated_at'
    ];

    public $sortable = ['id', 'created_at', 'updated_at'];

    /**
     * Get the checkin log for this history entry
     */
    public function checkinLog()
    {
        return $this->belongsTo('App\Models\CheckinLog', 'checkin_id');
    }

    /**
     * Get the user who created this history entry
     */
    public function creator()
    {
        return $this->belongsTo('App\Models\Admin', 'created_by');
    }
}
```

#### 1.4 Update Notification Model

**File:** `app/Models/Notification.php`

**Changes:**
```php
<?php
namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use Sortable;

    protected $fillable = [
        'sender_id', 'receiver_id', 'module_id', 'url',
        'notification_type', 'message', 'receiver_status', 
        'sender_status', 'seen'
    ];

    public $sortable = ['id', 'created_at', 'updated_at'];

    /**
     * Get the checkin log associated with this notification
     */
    public function checkinLog()
    {
        return $this->belongsTo('App\Models\CheckinLog', 'module_id');
    }

    /**
     * Get the sender of this notification
     */
    public function sender()
    {
        return $this->belongsTo('App\Models\Admin', 'sender_id');
    }

    /**
     * Get the receiver of this notification
     */
    public function receiver()
    {
        return $this->belongsTo('App\Models\Admin', 'receiver_id');
    }
}
```

**Note:** May need to add `sender_status` column to `notifications` table if it doesn't exist

---

### Phase 2: Broadcasting Event (Optional - Real-time Notifications)

#### 2.1 Create OfficeVisitNotificationCreated Event

**File:** `app/Events/OfficeVisitNotificationCreated.php`

**Actions:**
```php
<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class OfficeVisitNotificationCreated implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  int  $notificationId
     * @param  int  $receiverId
     * @param  array  $notificationData
     */
    public function __construct(
        public int $notificationId,
        public int $receiverId,
        public array $notificationData
    ) {
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int,\Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("user.{$this->receiverId}"),
        ];
    }

    /**
     * Customize the event name.
     */
    public function broadcastAs(): string
    {
        return 'OfficeVisitNotificationCreated';
    }

    /**
     * Data sent to the frontend upon broadcast.
     *
     * @return array<string,mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'notification' => $this->notificationData,
            'timestamp' => Carbon::now()->toIso8601String(),
        ];
    }
}
```

**Note:** This requires Laravel Broadcasting and Reverb/Pusher to be configured. If not using real-time notifications, this phase can be skipped or the broadcast calls can be wrapped in try-catch.

---

### Phase 3: Controller Updates

#### 3.1 Update OfficeVisitController

**File:** `app/Http/Controllers/Admin/OfficeVisitController.php`

**Key Changes:**

##### A. Import Statements
```php
use App\Models\CheckinLog;
use App\Models\CheckinHistory;  // Add back
use App\Models\ActivitiesLog;   // Keep for backward compatibility
use App\Events\OfficeVisitNotificationCreated;  // Add for broadcasting
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
```

##### B. Replace `checkin()` Method

**Current (broken):**
```php
public function checkin(Request $request){
    $requestData = $request->all();
    $obj = new \App\Models\CheckinLog;
    $obj->client_id = $requestData['contact'];
    $obj->user_id = $requestData['assignee'];
    $obj->visit_purpose = $requestData['message'];
    $obj->office = $requestData['office'];
    $obj->contact_type = $requestData['utype'];
    $obj->status = 0;
    // MISSING: $obj->is_archived = 0;
    $obj->date = date('Y-m-d');
    $saved = $obj->save();
    // ... rest of method
}
```

**New (from migrationmanager2):**
```php
public function checkin(Request $request){
    try {
        // Handle Select2 multiple select - get first value if array
        $contactValue = $request->input('contact');
        if (is_array($contactValue)) {
            $contactValue = !empty($contactValue) ? $contactValue[0] : null;
        }
        
        // Validate required fields
        $rules = [
            'assignee' => 'required|integer',
            'message' => 'required|string',
            'office' => 'required|integer',
            'utype' => 'required|string',
        ];
        
        $messages = [
            'assignee.required' => 'Please select an assignee.',
            'assignee.integer' => 'Invalid assignee selected.',
            'message.required' => 'Visit purpose is required.',
            'office.required' => 'Please select an office.',
            'office.integer' => 'Invalid office selected.',
            'utype.required' => 'Contact type is required. Please select a contact.',
        ];
        
        // Validate contact separately
        if (empty($contactValue)) {
            return redirect()->back()
                ->withErrors(['contact' => 'Please select a contact.'])
                ->withInput();
        }
        
        $contactId = (int) $contactValue;
        if ($contactId <= 0) {
            return redirect()->back()
                ->withErrors(['contact' => 'Please select a valid contact.'])
                ->withInput();
        }
        
        // Validate other fields
        $validated = $request->validate($rules, $messages);

        // Get validated data
        $assigneeId = (int) $validated['assignee'];
        $officeId = (int) $validated['office'];
        $visitPurpose = trim($validated['message'] ?? '');
        
        // Normalize contact type
        $utypeRaw = strtolower(trim($validated['utype'] ?? ''));
        if ($utypeRaw === 'lead') {
            $contactType = 'Lead';
        } elseif ($utypeRaw === 'client') {
            $contactType = 'Client';
        } else {
            $contactType = 'Client';
        }

        // Verify contact exists based on type
        if ($contactType == 'Lead') {
            $clientExists = \App\Models\Lead::where('id', $contactId)->exists();
        } else {
            $clientExists = Admin::where('role', '7')->where('id', $contactId)->exists();
        }

        if (!$clientExists) {
            return redirect()->back()->with('error', 'Selected contact does not exist.');
        }

        // Verify assignee exists
        $assigneeExists = Admin::where('role', '!=', '7')->where('id', $assigneeId)->exists();
        if (!$assigneeExists) {
            return redirect()->back()->with('error', 'Selected assignee does not exist.');
        }

        // Verify office exists
        $officeExists = \App\Models\Branch::where('id', $officeId)->exists();
        if (!$officeExists) {
            return redirect()->back()->with('error', 'Selected office does not exist.');
        }

        // Wrap all database operations in a transaction
        DB::beginTransaction();

        try {
            // Create CheckinLog
            $obj = new \App\Models\CheckinLog;
            $obj->client_id = $contactId;
            $obj->user_id = $assigneeId;
            $obj->visit_purpose = $visitPurpose;
            $obj->office = $officeId;
            $obj->contact_type = $contactType;
            $obj->status = 0;
            $obj->is_archived = 0; // FIX: Required NOT NULL constraint
            $obj->date = date('Y-m-d');
            
            if (!$obj->save()) {
                throw new \Exception('Failed to save check-in log.');
            }

            // Create Notification
            $notification = new \App\Models\Notification;
            $notification->sender_id = Auth::user()->id;
            $notification->receiver_id = $assigneeId;
            $notification->module_id = $obj->id;
            $notification->url = \URL::to('/office-visits/waiting');
            $notification->notification_type = 'officevisit';
            $notification->message = 'Office visit Assigned by ' . Auth::user()->first_name . ' ' . Auth::user()->last_name;
            $notification->seen = 0;
            $notification->receiver_status = 0;
            $notification->sender_status = 1;
            
            if (!$notification->save()) {
                throw new \Exception('Failed to save notification.');
            }

            // Get client information for broadcasting
            $client = $contactType == 'Lead' 
                ? \App\Models\Lead::find($contactId)
                : Admin::where('role', '7')->find($contactId);

            // Broadcast real-time notification (optional - wrap in try-catch)
            try {
                broadcast(new OfficeVisitNotificationCreated(
                    $notification->id,
                    $notification->receiver_id,
                    [
                        'id' => $notification->id,
                        'checkin_id' => $obj->id,
                        'message' => $notification->message,
                        'sender_name' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
                        'client_name' => $client ? $client->first_name . ' ' . $client->last_name : 'Unknown Client',
                        'visit_purpose' => $obj->visit_purpose,
                        'created_at' => $notification->created_at ? $notification->created_at->format('d/m/Y h:i A') : now()->format('d/m/Y h:i A'),
                        'url' => $notification->url
                    ]
                ));
            } catch (\Exception $broadcastException) {
                // Log broadcast error but don't fail the entire operation
                Log::warning('Failed to broadcast office visit notification', [
                    'notification_id' => $notification->id,
                    'error' => $broadcastException->getMessage()
                ]);
            }

            // Create CheckinHistory
            $checkinHistory = new CheckinHistory;
            $checkinHistory->subject = 'has created check-in';
            $checkinHistory->created_by = Auth::user()->id;
            $checkinHistory->checkin_id = $obj->id;
            
            if (!$checkinHistory->save()) {
                throw new \Exception('Failed to save check-in history.');
            }

            // OPTIONAL: Keep ActivitiesLog for backward compatibility
            // Create activity log for clients (not leads)
            if($contactType == 'Client') {
                $activityLog = new ActivitiesLog;
                $activityLog->client_id = $contactId;
                $activityLog->created_by = Auth::user()->id;
                $activityLog->subject = 'has created check-in';
                $activityLog->description = !empty($visitPurpose) ? 'Visit Purpose: ' . $visitPurpose : '';
                $activityLog->task_status = 0;
                $activityLog->pin = 0;
                $activityLog->save();
            }

            // Commit transaction
            DB::commit();

            return redirect()->back()->with('success', 'Checkin updated successfully');

        } catch (\Exception $e) {
            // Rollback transaction on any error
            DB::rollBack();
            
            // Log the error for debugging
            Log::error('Checkin creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['_token'])
            ]);

            return redirect()->back()->with('error', 'Failed to create check-in. Please try again.');
        }

    } catch (\Illuminate\Validation\ValidationException $e) {
        // Return validation errors
        return redirect()->back()
            ->withErrors($e->errors())
            ->withInput();
    } catch (\Exception $e) {
        // Catch any unexpected errors
        Log::error('Unexpected error in checkin method', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return redirect()->back()->with('error', Config::get('constants.server_error'));
    }
}
```

##### C. Restore CheckinHistory in Other Methods

**update_visit_comment():**
```php
public function update_visit_comment(Request $request){
    $objs = new CheckinHistory;
    $objs->subject = 'has commented';
    $objs->created_by = Auth::user()->id;
    $objs->checkin_id = $request->id;
    $objs->description = $request->visit_comment;
    $saved = $objs->save();
    if($saved){
        $response['status'] = true;
        $response['message'] = 'saved successfully';
    }else{
        $response['status'] = false;
        $response['message'] = 'Please try again';
    }
    echo json_encode($response);
}
```

**attend_session():**
```php
public function attend_session(Request $request){ 
    $obj = CheckinLog::find($request->id);
    $obj->sesion_start = date('Y-m-d H:i');
    $obj->wait_time = $request->waitcountdata;

    if($request->waitingtype == 1){
        $obj->status = 2;
        $t = 'attending';
    } else {
        $obj->status = 0;
        $obj->wait_type = 1;
        $t = 'waiting';
    }

    $saved = $obj->save();

    if($saved){
        $o = new \App\Models\Notification;
        $o->sender_id = Auth::user()->id;
        
        // TODO: Replace hardcoded receiver_id with configurable value
        // Option 1: Use assignee
        $o->receiver_id = $obj->user_id;
        
        // Option 2: Use config for receptionist
        // $o->receiver_id = config('constants.receptionist_user_id', $obj->user_id);
        
        $o->module_id = $request->id;
        $o->url = \URL::to('/office-visits/'.$t);
        $o->notification_type = 'officevisit';
        $o->message = 'Office Visit Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name;
        $o->seen = 0;
        $o->receiver_status = 0;
        $o->sender_status = 1;
        $o->save();
        
        // Broadcast notification (optional)
        try {
            $client = $obj->contact_type == 'Lead' 
                ? \App\Models\Lead::find($obj->client_id)
                : Admin::where('role', '7')->find($obj->client_id);
            
            broadcast(new OfficeVisitNotificationCreated(
                $o->id,
                $o->receiver_id,
                [
                    'id' => $o->id,
                    'checkin_id' => $obj->id,
                    'message' => $o->message,
                    'sender_name' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
                    'client_name' => $client ? $client->first_name . ' ' . $client->last_name : 'Unknown Client',
                    'visit_purpose' => $obj->visit_purpose,
                    'created_at' => $o->created_at ? $o->created_at->format('d/m/Y h:i A') : now()->format('d/m/Y h:i A'),
                    'url' => $o->url
                ]
            ));
        } catch (\Exception $e) {
            Log::warning('Failed to broadcast attend session notification', [
                'error' => $e->getMessage()
            ]);
        }
    }

    // Create CheckinHistory
    $objs = new CheckinHistory;
    $objs->subject = 'has started session';
    $objs->created_by = Auth::user()->id;
    $objs->checkin_id = $request->id;
    $saved = $objs->save();
    
    // OPTIONAL: Keep ActivitiesLog for backward compatibility
    if($obj->contact_type == 'Client') {
        $activityLog = new ActivitiesLog;
        $activityLog->client_id = $obj->client_id;
        $activityLog->created_by = Auth::user()->id;
        $activityLog->subject = 'has started check-in session';
        $activityLog->description = !empty($obj->visit_purpose) ? 'Visit Purpose: ' . $obj->visit_purpose : '';
        $activityLog->task_status = 0;
        $activityLog->pin = 0;
        $activityLog->save();
    }
    
    if($saved){
        $response['status'] = true;
        $response['message'] = 'saved successfully';
    }else{
        $response['status'] = false;
        $response['message'] = 'Please try again';
    }
    echo json_encode($response);
}
```

**complete_session():**
```php
public function complete_session(Request $request){
    $obj = CheckinLog::find($request->id);
    $obj->sesion_end = date('Y-m-d H:i');
    $obj->attend_time = $request->attendcountdata;
    $obj->status = 1;
    $saved = $obj->save();

    // Create CheckinHistory
    $objs = new CheckinHistory;
    $objs->subject = 'has completed session';
    $objs->created_by = Auth::user()->id;
    $objs->checkin_id = $request->id;
    $saved = $objs->save();
    
    // OPTIONAL: Keep ActivitiesLog for backward compatibility
    if($obj->contact_type == 'Client') {
        $activityLog = new ActivitiesLog;
        $activityLog->client_id = $obj->client_id;
        $activityLog->created_by = Auth::user()->id;
        $activityLog->subject = 'has completed check-in session';
        $description = !empty($obj->visit_purpose) ? 'Visit Purpose: ' . $obj->visit_purpose : '';
        if(!empty($obj->attend_time)) {
            $description .= ($description ? ' | ' : '') . 'Session Duration: ' . $obj->attend_time;
        }
        $activityLog->description = $description;
        $activityLog->task_status = 0;
        $activityLog->pin = 0;
        $activityLog->save();
    }
    
    if($saved){
        $response['status'] = true;
        $response['message'] = 'saved successfully';
    }else{
        $response['status'] = false;
        $response['message'] = 'Please try again';
    }
    echo json_encode($response);
}
```

##### D. Update `getcheckin()` to Show CheckinHistory Logs

**Current (disabled):**
```php
// NOTE: CheckinHistory removed - table dropped
// $logslist = CheckinHistory::where('checkin_id',$CheckinLog->id)->orderby('created_at', 'DESC')->get();
// ... (commented out loop)
echo '<p class="text-muted">Check-in history logs have been removed.</p>';
```

**New (restored):**
```php
// Show CheckinHistory logs
$logslist = CheckinHistory::where('checkin_id',$CheckinLog->id)->orderby('created_at', 'DESC')->get();
foreach($logslist as $llist){
    $admin = \App\Models\Admin::where('id', $llist->created_by)->first();
    ?>
    <div class="logsitem">
        <div class="row">
            <div class="col-md-7">
                <span class="ag-avatar"><?php echo substr($admin->first_name, 0, 1); ?></span>
                <span class="text_info"><span><?php echo $admin->first_name; ?></span><?php echo $llist->subject; ?></span>
            </div>
            <div class="col-md-5">
                <span class="logs_date"><?php echo date('d M Y h:i A', strtotime($llist->created_at)); ?></span>
            </div>
            <?php if($llist->description != ''){ ?>
            <div class="col-md-12 logs_comment">
                <p><?php echo $llist->description; ?></p>
            </div>
            <?php } ?>
        </div>
    </div>
<?php }
```

##### E. Add Broadcasting to `change_assignee()`

```php
public function change_assignee(Request $request){
    $objs = CheckinLog::find($request->id);
    $objs->user_id = $request->assinee;

    $saved = $objs->save();
    
    if($objs->status == 2){
        $t = 'attending';
    }else if($objs->status == 1){
        $t = 'completed';
    }else if($objs->status == 0){
        $t = 'waiting';
    }
    
    if($saved){
        $o = new \App\Models\Notification;
        $o->sender_id = Auth::user()->id;
        $o->receiver_id = $request->assinee;
        $o->module_id = $request->id;
        $o->url = \URL::to('/office-visits/'.$t);
        $o->notification_type = 'officevisit';
        $o->message = 'Office Visit Assigned by '.Auth::user()->first_name.' '.Auth::user()->last_name;
        $o->seen = 0;
        $o->receiver_status = 0;
        $o->sender_status = 1;
        $o->save();
        
        // Broadcast notification (optional)
        try {
            $client = $objs->contact_type == 'Lead' 
                ? \App\Models\Lead::find($objs->client_id)
                : Admin::where('role', '7')->find($objs->client_id);
            
            broadcast(new OfficeVisitNotificationCreated(
                $o->id,
                $o->receiver_id,
                [
                    'id' => $o->id,
                    'checkin_id' => $objs->id,
                    'message' => $o->message,
                    'sender_name' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
                    'client_name' => $client ? $client->first_name . ' ' . $client->last_name : 'Unknown Client',
                    'visit_purpose' => $objs->visit_purpose,
                    'created_at' => $o->created_at ? $o->created_at->format('d/m/Y h:i A') : now()->format('d/m/Y h:i A'),
                    'url' => $o->url
                ]
            ));
        } catch (\Exception $e) {
            Log::warning('Failed to broadcast change assignee notification', [
                'error' => $e->getMessage()
            ]);
        }
        
        $response['status'] = true;
        $response['message'] = 'Updated successfully';
    }else{
        $response['status'] = false;
        $response['message'] = 'Please try again';
    }
    echo json_encode($response);
}
```

---

### Phase 4: Configuration Updates (Optional)

#### 4.1 Add Receptionist User ID to Config

**File:** `config/constants.php`

**Add:**
```php
// Office Visit / Check-in Configuration
'receptionist_user_id' => env('RECEPTIONIST_USER_ID', null),
```

**File:** `.env`

**Add:**
```
# Receptionist user ID for office visit notifications
RECEPTIONIST_USER_ID=22136
```

**Usage in controller:**
```php
// Instead of hardcoded 22136
$o->receiver_id = config('constants.receptionist_user_id', $obj->user_id);
```

---

### Phase 5: Frontend Updates (If Needed)

#### 5.1 Verify View Layout Compatibility

**Check:**
- migrationmanager2 uses: `@extends('layouts.crm_client_detail')`
- bansalcrm2 uses: `@extends('layouts.admin')`

**Action:** Keep bansalcrm2's layout unless a unified layout is desired.

#### 5.2 Real-time Notification Listener (Optional)

If implementing broadcasting, add listener to layout file:

**File:** `resources/views/layouts/admin.blade.php` (or main layout)

**Add before `</body>`:**
```javascript
@auth('admin')
<script>
// Listen for office visit notifications
window.Echo.private('user.{{ Auth::user()->id }}')
    .listen('.OfficeVisitNotificationCreated', (e) => {
        console.log('Office visit notification received:', e);
        
        // Show toast notification
        toastr.info(e.notification.message, 'New Office Visit');
        
        // Update notification count badge
        updateNotificationCount();
        
        // Optionally reload the page if on office-visits page
        if (window.location.pathname.includes('office-visits')) {
            // Refresh the check-in list
            location.reload();
        }
    });

function updateNotificationCount() {
    // Fetch updated notification count
    fetch('{{ route("dashboard.fetch-office-visit-notifications") }}')
        .then(response => response.json())
        .then(data => {
            // Update badge count
            const badge = document.querySelector('.notification-badge');
            if (badge && data.count > 0) {
                badge.textContent = data.count;
                badge.style.display = 'inline-block';
            }
        });
}
</script>
@endauth
```

**Note:** This requires:
- Laravel Echo configured
- Reverb/Pusher set up
- Private channel authentication

---

## Implementation Steps (Ordered)

### Step 1: Database Migration
1. Create and run migration to restore `checkin_histories` table
2. Verify migration succeeded: `php artisan migrate:status`
3. Check table exists: `SELECT * FROM checkin_histories LIMIT 1;`

### Step 2: Model Updates
1. Update `app/Models/CheckinLog.php`
2. Create `app/Models/CheckinHistory.php`
3. Update `app/Models/Notification.php`
4. Verify models load without errors

### Step 3: Create Event (Optional)
1. Create `app/Events/OfficeVisitNotificationCreated.php`
2. Test event creation doesn't break app (even if broadcasting not configured)

### Step 4: Controller Updates
1. **Backup current controller:** `cp app/Http/Controllers/Admin/OfficeVisitController.php app/Http/Controllers/Admin/OfficeVisitController.php.backup`
2. Update import statements
3. Replace `checkin()` method
4. Update `update_visit_comment()`
5. Update `attend_session()`
6. Update `complete_session()`
7. Update `change_assignee()`
8. Update `getcheckin()` to show logs

### Step 5: Configuration (Optional)
1. Add `receptionist_user_id` to `config/constants.php`
2. Add to `.env` file
3. Update `attend_session()` to use config value

### Step 6: Testing

**Test Checklist:**
- [ ] Create new check-in (should not error with `is_archived` NULL)
- [ ] Verify check-in appears in "Waiting" tab
- [ ] Click on check-in to view details modal
- [ ] Verify "Logs" section shows "has created check-in" entry
- [ ] Add a comment via "Comment" textarea
- [ ] Verify comment appears in logs
- [ ] Click "Attend Session" button
- [ ] Verify session moves to "Attending" tab
- [ ] Verify "has started session" appears in logs
- [ ] Click "Complete Session" button
- [ ] Verify session moves to "Completed" tab
- [ ] Verify "has completed session" appears in logs
- [ ] Change assignee
- [ ] Verify assignee updated
- [ ] Test with Lead contact type
- [ ] Test with Client contact type
- [ ] Verify ActivitiesLog created for clients (backward compatibility)
- [ ] Check error logs for any warnings/errors
- [ ] Test validation (empty contact, empty assignee, etc.)
- [ ] Verify transaction rollback on errors

**Broadcasting Tests (if implemented):**
- [ ] Create check-in and verify real-time notification received
- [ ] Change assignee and verify notification
- [ ] Attend session and verify notification

---

## Rollback Plan

If issues occur after deployment:

### Immediate Rollback
1. Restore backed-up controller:
   ```bash
   cp app/Http/Controllers/Admin/OfficeVisitController.php.backup app/Http/Controllers/Admin/OfficeVisitController.php
   ```

2. Drop `checkin_histories` table if needed:
   ```sql
   DROP TABLE IF EXISTS checkin_histories CASCADE;
   ```

3. Clear caches:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

### Partial Rollback (Keep CheckinHistory, Fix Only Critical Bug)

If full migration is not desired, apply minimal fix:

**File:** `app/Http/Controllers/Admin/OfficeVisitController.php`

**Change ONLY line 45 in `checkin()` method:**
```php
$obj->is_archived = 0; // Add this single line
```

This fixes the NOT NULL violation without restoring CheckinHistory functionality.

---

## Risk Assessment

### High Risk
- ❌ **Database migration on production** - Could fail if sequences/constraints are wrong
  - **Mitigation:** Test on staging first, have backup, verify migration up/down

### Medium Risk
- ⚠️ **CheckinHistory restoration** - If old data exists, foreign key constraints might fail
  - **Mitigation:** Check for orphaned records before migration, add ON DELETE CASCADE
  
- ⚠️ **Broadcasting implementation** - If Reverb/Pusher not configured, could cause errors
  - **Mitigation:** Wrap all broadcast() calls in try-catch, don't let broadcasting failure stop check-in creation

### Low Risk
- ✅ **Model updates** - Low impact, just adds fields and relationships
- ✅ **Notification model** - Already exists, just enhancing
- ✅ **Config changes** - Safe, backward compatible

---

## Decision Points

### Decision 1: Restore CheckinHistory or Not?

**Option A: Full Restoration (Recommended)**
- ✅ Audit trail for all check-in actions
- ✅ Comment functionality works
- ✅ Matches migrationmanager2 behavior
- ❌ More complex migration

**Option B: Minimal Fix Only**
- ✅ Simple: just add `is_archived = 0`
- ✅ No schema changes
- ❌ No audit trail
- ❌ No comments feature
- ❌ Diverges from migrationmanager2

**Recommendation:** Option A (Full Restoration) - The audit trail is valuable for compliance and debugging.

---

### Decision 2: Keep ActivitiesLog or Not?

**Option A: Keep Both (Recommended)**
- ✅ Backward compatibility
- ✅ No data loss
- ✅ CheckinHistory for check-in-specific logs
- ✅ ActivitiesLog for client timeline
- ❌ Slight duplication

**Option B: Remove ActivitiesLog**
- ✅ Less duplication
- ❌ Breaks existing client activity timeline
- ❌ Requires updating other controllers/views

**Recommendation:** Option A (Keep Both) - They serve different purposes and removing ActivitiesLog could break other features.

---

### Decision 3: Implement Broadcasting or Not?

**Option A: Implement Broadcasting**
- ✅ Real-time notifications
- ✅ Better UX
- ✅ Matches migrationmanager2
- ❌ Requires Reverb/Pusher setup
- ❌ More complex

**Option B: Skip Broadcasting**
- ✅ Simpler implementation
- ✅ Faster deployment
- ❌ No real-time updates
- ❌ Users must refresh page

**Recommendation:** Option B initially (Skip Broadcasting), then implement as Phase 2 after core functionality is stable.

---

### Decision 4: Hardcoded Receptionist ID

**Option A: Use Config (Recommended)**
- ✅ Environment-specific
- ✅ Easy to change
- ✅ More flexible
- ❌ Requires `.env` update

**Option B: Use Assignee**
- ✅ No config needed
- ✅ Notification goes to person handling check-in
- ❌ Changes original behavior

**Option C: Keep Hardcoded**
- ✅ No changes needed
- ❌ Fragile, environment-specific
- ❌ Will break in other environments

**Recommendation:** Option A (Use Config) with fallback to assignee if not set.

---

## Timeline Estimate

**Minimal Fix (is_archived only):** 30 minutes
- Add one line to controller
- Test check-in creation
- Deploy

**Full Migration (Recommended):** 4-6 hours
- Phase 1 (Database & Models): 1-2 hours
- Phase 2 (Event - skip initially): 0 hours
- Phase 3 (Controller): 2-3 hours
- Phase 4 (Config): 15 minutes
- Phase 5 (Frontend - skip initially): 0 hours
- Testing: 45 minutes

**With Broadcasting:** Add 2-3 hours
- Configure Reverb/Pusher
- Add event listeners to frontend
- Test real-time notifications

---

## Success Criteria

1. ✅ Office check-ins can be created without `is_archived` NOT NULL error
2. ✅ Check-in history logs appear in the detail modal
3. ✅ Comments can be added to check-ins
4. ✅ Attend session and complete session work correctly
5. ✅ Both Lead and Client contact types work
6. ✅ Validation catches invalid inputs
7. ✅ Errors are logged properly
8. ✅ Transactions rollback on failure
9. ✅ No regressions in existing functionality
10. ✅ ActivitiesLog still created for clients (backward compatibility)

---

## Post-Migration Tasks

1. **Monitor error logs** for 48 hours after deployment
2. **Verify no orphaned records** in checkin_histories
3. **Update documentation** if any user-facing changes
4. **Train staff** on restored comment functionality
5. **Consider implementing broadcasting** as Phase 2
6. **Remove hardcoded user IDs** and use config-based approach
7. **Add unit tests** for checkin creation and validation
8. **Review and optimize** CheckinHistory queries if performance issues

---

## Files to Modify/Create

### New Files
1. `database/migrations/2026_01_29_000001_restore_checkin_histories_table.php`
2. `app/Models/CheckinHistory.php`
3. `app/Events/OfficeVisitNotificationCreated.php` (optional)

### Modified Files
1. `app/Models/CheckinLog.php`
2. `app/Models/Notification.php`
3. `app/Http/Controllers/Admin/OfficeVisitController.php`
4. `config/constants.php` (optional)
5. `.env` (optional)

### Backup Before Modifying
- `app/Http/Controllers/Admin/OfficeVisitController.php`

---

## Questions to Resolve Before Implementation

1. **CheckinHistory Restoration:** Confirm decision to restore CheckinHistory table
2. **Broadcasting:** Deploy without broadcasting initially, or implement immediately?
3. **Receptionist ID:** Use config-based approach or keep hardcoded?
4. **ActivitiesLog:** Keep creating ActivitiesLog alongside CheckinHistory?
5. **Testing Environment:** Is there a staging database to test migration on?
6. **Backup Plan:** Is there a database backup before running migration?
7. **Reverb/Pusher:** Is broadcasting infrastructure available if needed?

---

## Appendix: Key Differences Summary

| Feature | bansalcrm2 (Current) | migrationmanager2 (Target) |
|---------|----------------------|----------------------------|
| **CheckinLog.$fillable** | `['id', 'created_at', 'updated_at']` | Full set of fields including `is_archived` |
| **is_archived on create** | ❌ Missing (causes error) | ✅ Set to 0 |
| **CheckinHistory** | ❌ Table dropped | ✅ Active and used |
| **Validation** | ❌ Minimal | ✅ Comprehensive with custom messages |
| **Error Handling** | ❌ Basic | ✅ Try-catch with logging and transaction |
| **Broadcasting** | ❌ None | ✅ Real-time via Reverb |
| **Notification Model** | Basic | Enhanced with relationships |
| **ActivitiesLog** | ✅ Used for clients | ✅ Used for clients (kept in migration) |
| **Comment Feature** | ❌ Disabled | ✅ Enabled |
| **Audit Trail** | ❌ No CheckinHistory | ✅ Full CheckinHistory |
| **Receptionist ID** | 22136 (hardcoded) | 36608 (hardcoded) - both need fix |

---

## Conclusion

This plan provides a comprehensive path to migrate the improved check-in system from migrationmanager2 to bansalcrm2. The recommended approach is:

1. **Phase 1:** Database & Model updates (required)
2. **Phase 2:** Skip broadcasting initially
3. **Phase 3:** Controller updates with full validation and error handling (required)
4. **Phase 4:** Add config-based receptionist ID (recommended)
5. **Phase 5:** Skip frontend changes initially
6. **Future:** Add broadcasting and real-time notifications after core is stable

This fixes the immediate `is_archived` error while also restoring valuable CheckinHistory functionality that was previously removed.

**Next Steps:**
1. Review and approve this plan
2. Schedule deployment window
3. Create database backup
4. Execute migration on staging environment
5. Test thoroughly
6. Deploy to production
7. Monitor for 48 hours

---

**Document Version:** 1.0  
**Created:** 2026-01-29  
**Status:** Awaiting Approval - Do Not Apply Yet
