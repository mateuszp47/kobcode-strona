<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Inquiry extends Model {
    protected $fillable = ['name','email','company','phone','message','is_read'];
    protected function casts(): array { return ['is_read'=>'boolean']; }
}
