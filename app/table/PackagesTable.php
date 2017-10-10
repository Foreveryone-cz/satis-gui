<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 10.10.2017
 * Time: 12:17
 */

namespace Table;


use Davajlama\SchemaBuilder\Schema\Table;
use Davajlama\SchemaBuilder\Schema\Type;

class PackagesTable extends Table
{
    public function __construct()
    {
        parent::__construct('packages');
        $this->createId();
        $this->createColumn('type', Type::varcharType(255));
        $this->createColumn('url', Type::varcharType(255))->unique();
        $this->createColumn('group', Type::varcharType(255));
    }

}