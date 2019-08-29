<?php
namespace App\Model\Doc;

use App\Model\Base\BaseModel;
use App\Model\Base\Model;

class DocModel extends BaseModel
{
    protected $connection = 'mysql';
    protected $table = 'doc_list';
    protected $primaryKey=['id'];
    public $incrementing=false;
    protected $keyType='string';
}
