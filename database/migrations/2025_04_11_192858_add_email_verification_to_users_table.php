<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email_otp')->nullable();
            $table->boolean('email_verified')->default(false);
            $table->timestamp('email_otp_expires_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['email_otp', 'email_verified', 'email_otp_expires_at']);
        });
    }
};
