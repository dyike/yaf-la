<?php
$str =  <<< TEMPLATE
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class %s extends EloquentModel
{

}

TEMPLATE;
return $str;