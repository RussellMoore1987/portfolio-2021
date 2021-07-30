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
        $caseStudies->each(function ($caseStudy) use ($images) { 
            $caseStudy->images()->attach(
                $images->random(rand(1, 5))->pluck('id')->toArray(),
                ['sort_order' => rand(1, 100)]
            ); 

            // set featured image
            // ! start Here ************************************************************** also add resources*** table
            // $review->product()->detach()
            // caseStudy->images->find($caseStudy->images[0]->id);
            $caseStudy->images()->sync([$caseStudy->images[0]->id], ['is_featured_img' => 1, 'sort_order' => 1]);
            // $caseStudy->images()->random()->is_featured();
            // dd($caseStudy->images[0]->is_featured());
        });
    }
}
