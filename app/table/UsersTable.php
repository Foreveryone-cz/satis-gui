<?php
/**
 * Created by PhpStorm.
 * User: David
 * Date: 10.10.2017
 * Time: 10:51
 */

namespace Table;


use Davajlama\SchemaBuilder\Schema\Table;
use Davajlama\SchemaBuilder\Schema\Type;

class UsersTable extends Table
{
    /**
     * UsersTable constructor.
     */
    public function __construct()
    {
        parent::__construct('users');

        $this->createId();
        $this->createColumn('username', Type::varcharType(255))->unique();
        $this->createColumn('password', Type::varcharType(255));
    }

}