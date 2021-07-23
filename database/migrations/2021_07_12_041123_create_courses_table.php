<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("slug");
            $table->string("description");
            $table->float("price")->default(99.99);
            $table->string("image");
            $table->string("category");
            $table->boolean("published")->default(false);
            $table->boolean("paid");
            $table->integer("instructor_id");
            $table->string("instructor_name");
            $table->integer("number_of_lessons")->default(0);
            $table->integer("total_enrollment")->default(0);
            $table->float("total_earning_course")->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courses');
    }
}
