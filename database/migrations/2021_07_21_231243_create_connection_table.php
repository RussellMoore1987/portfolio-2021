<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConnectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_tag', function (Blueprint $table) {
            $table->foreignId('project_id');
            $table->foreignId('tag_id');

            $table->primary(['project_id', 'tag_id']);
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('tag_id')->references('id')->on('tags');
        });

        Schema::create('category_project', function (Blueprint $table) {
            $table->foreignId('project_id');
            $table->foreignId('category_id');

            $table->primary(['project_id', 'category_id']);
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('category_id')->references('id')->on('categories');
        });

        Schema::create('image_project', function (Blueprint $table) {
            $table->foreignId('project_id');
            $table->foreignId('image_id');
            $table->tinyInteger('is_featured_img')->default(0);
            $table->tinyInteger('sort_order')->default(100);

            $table->primary(['project_id', 'image_id']);
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('image_id')->references('id')->on('images');
        });

        Schema::create('case_study_tag', function (Blueprint $table) {
            $table->foreignId('case_study_id');
            $table->foreignId('tag_id');

            $table->primary(['case_study_id', 'tag_id']);
            $table->foreign('case_study_id')->references('id')->on('case_studies');
            $table->foreign('tag_id')->references('id')->on('tags');
        });

        Schema::create('case_study_category', function (Blueprint $table) {
            $table->foreignId('case_study_id');
            $table->foreignId('category_id');

            $table->primary(['case_study_id', 'category_id']);
            $table->foreign('case_study_id')->references('id')->on('case_studies');
            $table->foreign('category_id')->references('id')->on('categories');
        });

        Schema::create('case_study_image', function (Blueprint $table) {
            $table->foreignId('case_study_id');
            $table->foreignId('image_id');
            $table->tinyInteger('is_featured_img')->default(0);
            $table->tinyInteger('sort_order')->default(100);

            $table->primary(['case_study_id', 'image_id']);
            $table->foreign('case_study_id')->references('id')->on('case_studies');
            $table->foreign('image_id')->references('id')->on('images');
        });

        Schema::create('post_tag', function (Blueprint $table) {
            $table->foreignId('post_id');
            $table->foreignId('tag_id');

            $table->primary(['post_id', 'tag_id']);
            $table->foreign('post_id')->references('id')->on('posts');
            $table->foreign('tag_id')->references('id')->on('tags');
        });

        Schema::create('category_post', function (Blueprint $table) {
            $table->foreignId('post_id');
            $table->foreignId('category_id');

            $table->primary(['post_id', 'category_id']);
            $table->foreign('post_id')->references('id')->on('posts');
            $table->foreign('category_id')->references('id')->on('categories');
        });

        Schema::create('image_post', function (Blueprint $table) {
            $table->foreignId('post_id');
            $table->foreignId('image_id');
            $table->tinyInteger('is_featured_img')->default(0);
            $table->tinyInteger('sort_order')->default(100);

            $table->primary(['post_id', 'image_id']);
            $table->foreign('post_id')->references('id')->on('posts');
            $table->foreign('image_id')->references('id')->on('images');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_tag');
        Schema::dropIfExists('category_project');
        Schema::dropIfExists('image_project');
        Schema::dropIfExists('case_study_tag');
        Schema::dropIfExists('case_study_category');
        Schema::dropIfExists('case_study_image');
        Schema::dropIfExists('post_tag');
        Schema::dropIfExists('category_post');
        Schema::dropIfExists('image_post');
    }
}
