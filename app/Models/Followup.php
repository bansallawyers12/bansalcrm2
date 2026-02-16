<?php
namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;

class Followup extends Model
{	use Sortable;

	protected $fillable = [
        'id', 'created_at', 'updated_at'
    ];
	
	public $sortable = ['id', 'created_at', 'updated_at'];
	
	/**
     * Creator (Staff or Admin) who created the followup.
     * Uses Staff - if user_id stores Admin id, resolve in view via Staff::find() ?? Admin::find().
     */
	public function staff()
    {
        return $this->belongsTo('App\Models\Staff', 'user_id', 'id');
    }
	 public function post()
    {
        return $this->belongsTo('App\Models\Lead','lead_id');
    }
	public function followutype()
    {
        return $this->belongsTo('App\Models\FollowupType','followup_type','type');
    }

}