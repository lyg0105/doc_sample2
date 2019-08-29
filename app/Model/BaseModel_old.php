<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    public $x_column_arr=array();
    public $write_except_col_arr=array();
}
