<?php

namespace denis303\user;

abstract class BaseCreateUserTableMigration extends \denis303\codeigniter4\Migration
{

    abstract function up();

    abstract function down();

}