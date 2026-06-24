<?php

namespace App\Models;

use Kyslik\ColumnSortable\Sortable;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use Sortable;
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	
	protected $fillable = [
        'id', 'client_id', 'user_id', 'product_id', 'enrolment_type', 'partner_id', 'branch', 
        'workflow', 'stage', 'status', 'checklist_sheet_status', 'checklist_sent_at', 'created_at', 'updated_at'
    ];

    public const ENROLMENT_TYPE_TRANSFER = 'transfer_option';
    public const ENROLMENT_TYPE_COURSE_PROGRESSION = 'course_progression';

    public static function enrolmentTypeOptions(): array
    {
        return [
            self::ENROLMENT_TYPE_TRANSFER => 'Transfer',
            self::ENROLMENT_TYPE_COURSE_PROGRESSION => 'Course progression',
        ];
    }

    public static function enrolmentTypeLabel(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        return self::enrolmentTypeOptions()[$value] ?? $value;
    }

    public static function normalizeEnrolmentType(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        $options = self::enrolmentTypeOptions();
        if (array_key_exists($value, $options)) {
            return $value;
        }

        $normalized = strtolower(trim($value));
        foreach ($options as $key => $label) {
            if ($normalized === strtolower($key) || $normalized === strtolower($label)) {
                return $key;
            }
        }

        return $value;
    }

    public static function enrolmentTypeSelectHtml(int $applicationId, ?string $currentValue, string $cssClass = 'form-control form-control-sm enrolment-type-field'): string
    {
        $currentValue = self::normalizeEnrolmentType($currentValue);
        $html = '<select class="'.e($cssClass).'" data-application-id="'.(int) $applicationId.'" data-enrolment-type="'.e($currentValue).'">';
        $html .= '<option value=""'.($currentValue === '' ? ' selected="selected"' : '').'>Select</option>';

        foreach (self::enrolmentTypeOptions() as $value => $label) {
            $selected = $currentValue === $value ? ' selected="selected"' : '';
            $html .= '<option value="'.e($value).'"'.$selected.'>'.e($label).'</option>';
        }

        $html .= '</select>';

        return $html;
    }
	
	public $sortable = ['id', 'created_at', 'updated_at'];
    
    public function application_assignee()
    {
        return $this->belongsTo('App\Models\Staff', 'user_id', 'id');
    }
	
	public function product()
	{
		return $this->belongsTo('App\Models\Product', 'product_id', 'id');
	}
	
	public function partner()
	{
		return $this->belongsTo('App\Models\Partner', 'partner_id', 'id');
	}
	
	public function branch()
	{
		return $this->belongsTo('App\Models\PartnerBranch', 'branch', 'id');
	}
	
	public function workflow()
	{
		return $this->belongsTo('App\Models\Workflow', 'workflow', 'id');
	}
	
	public function invoices()
	{
		return $this->hasMany('App\Models\Invoice', 'application_id', 'id');
	}
	
}

