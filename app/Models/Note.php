<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Kyslik\ColumnSortable\Sortable;

class Note extends BaseModel
{
    use Notifiable;
	use Sortable;

    protected $fillable = [
        'id', 'user_id', 'client_id', 'title', 'mail_id', 'type', 'assigned_to', 'pin', 'action_assign_date', 'is_action', 'status', 'followup_outcome', 'description', 'created_at', 'updated_at', 'task_group', 'mobile_number',
    ];
   
	public $sortable = ['id', 'created_at', 'updated_at','task_group','action_assign_date'];
	
	
    public function noteClient()
    {
        return $this->belongsTo('App\Models\Admin','client_id','id');
    }
    
    public function notePartner()
    {
        return $this->belongsTo('App\Models\Partner','client_id','id');
    }

    /**
     * Staff who created the note.
     */
    public function noteStaff()
    {
        return $this->belongsTo('App\Models\Staff', 'user_id', 'id');
    }

    /** @deprecated Use noteStaff() instead. */
    public function noteUser()
    {
        return $this->belongsTo('App\Models\Staff', 'user_id', 'id');
    }

    public function assigned_user()
    {
        return $this->belongsTo('App\Models\Staff', 'assigned_to', 'id');
    }

    /**
     * Personal tasks created via "Add Action" may have no linked client.
     */
    public function isPersonalTaskWithoutClient(): bool
    {
        $taskGroup = strtolower(trim((string) ($this->task_group ?? '')));

        return in_array($taskGroup, ['personal task', 'personal action'], true) && empty($this->client_id);
    }

	
}
