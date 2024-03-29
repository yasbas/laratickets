<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

class CreateDefaultRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'support_agent']);
        Role::create(['name' => 'user']);
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }

}
