<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeoPage extends Model
{	
	protected $fillable = [
		'id', 'page_title', 'page_slug', 'meta_title', 'meta_keyword', 'meta_desc', 'created_at', 'updated_at'
    ];
}