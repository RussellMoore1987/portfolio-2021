<?php

namespace Database\Seeders;

use App\Models\SkillType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // TODO:  add resources*** table
        $caseStudies = \App\Models\CaseStudy::factory(50)->create();
        $projects = \App\Models\Project::factory(50)->create();
        $content = \App\Models\Content::factory(50)->create();
        $experiences = \App\Models\Experience::factory(10)->create();
        $images = \App\Models\Image::factory(100)->create();
        $posts = \App\Models\Post::factory(100)->create();
        $categories = \App\Models\Category::factory(20)->create();
        $tags = \App\Models\Tag::factory(30)->create();
        $skillTypes = \App\Models\SkillType::factory(2)->create();
        $skills = \App\Models\Skill::factory(20)->make()->each(function ($skill) use ($skillTypes, $tags)
        {
            $skill->Skill_type_id = $skillTypes->random()->id;
            $skill->tag_id = $tags->random()->id;
            $skill->save();
        });
        $WorkHistoryTypes = \App\Models\WorkHistoryType::factory(4)->create();
        $WorkHistories = \App\Models\WorkHistory::factory(10)->make()->each(function ($workHistory) use ($WorkHistoryTypes)
        {
            $workHistory->work_history_type_id = $WorkHistoryTypes->random()->id;
            $workHistory->save();
        });
        
        // Connection tables / pivot tables
        $caseStudies->each(function ($caseStudy) use ($images, $tags, $categories) { 
            $caseStudy->images()->attach(
                $images->random(rand(2, 5))->pluck('id')->toArray(),
                ['sort_order' => rand(1, 100)]
            ); 

            $caseStudy->tags()->attach(
                $tags->random(rand(2, 6))->pluck('id')->toArray()
            ); 

            $caseStudy->categories()->attach(
                $categories->random(rand(2, 6))->pluck('id')->toArray()
            ); 

            // set featured image // TODO: Turn this into a function
            $imageId = $caseStudy->images[0]->id;
            $caseStudy->images()->updateExistingPivot($imageId, [
                'is_featured_img' => 1,
                'sort_order' => 1
            ]);
        });

        $projects->each(function ($project) use ($images, $tags, $categories) { 
            $project->images()->attach(
                $images->random(rand(2, 5))->pluck('id')->toArray(),
                ['sort_order' => rand(1, 100)]
            ); 

            $project->tags()->attach(
                $tags->random(rand(2, 6))->pluck('id')->toArray()
            ); 

            $project->categories()->attach(
                $categories->random(rand(2, 6))->pluck('id')->toArray()
            ); 

            // set featured image // TODO: Turn this into a function
            $imageId = $project->images[0]->id;
            $project->images()->updateExistingPivot($imageId, [
                'is_featured_img' => 1,
                'sort_order' => 1
            ]);
        });

        $posts->each(function ($post) use ($images, $tags, $categories) { 
            $post->images()->attach(
                $images->random(rand(2, 5))->pluck('id')->toArray(),
                ['sort_order' => rand(1, 100)]
            ); 

            $post->tags()->attach(
                $tags->random(rand(2, 6))->pluck('id')->toArray()
            ); 

            $post->categories()->attach(
                $categories->random(rand(2, 6))->pluck('id')->toArray()
            ); 

            // set featured image // TODO: Turn this into a function
            $imageId = $post->images[0]->id;
            $post->images()->updateExistingPivot($imageId, [
                'is_featured_img' => 1,
                'sort_order' => 1
            ]);
        });
    }
}
