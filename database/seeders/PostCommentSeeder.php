<?php

namespace Database\Seeders;

use App\Helpers\MyApp;
use App\Helpers\PermissionsProcess;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\Role;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class PostCommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleUser = Role::query()->where("name",PermissionsProcess::ROLE_USER)->first();
        $faker = Faker::create();
        for ($i = 1 ; $i<=10 ; $i++){
            $user = User::query()->create([
                "name" => $faker->name(),
                "first_name" => $faker->firstName(),
                "last_name" => $faker->lastName(),
                "role" => $roleUser->name,
                "email" => $faker->email(),
                "password" => Hash::make(MyApp::PASSWORD),
                'phone' => $faker->phoneNumber(),
                'address' => $faker->address(),
                'image' => $faker->imageUrl(),
                "email_verified_at" => now(),
            ]);

            $user->addRole($roleUser);

            $post = Post::query()->create([
                "user_id" => $user->id,
                "title" => $faker->title(),
                "content" => $faker->text(),
                'image' => $faker->imageUrl(),
            ]);
        }

        $idsUsers = User::query()->where("role",PermissionsProcess::ROLE_USER)->get()->pluck("id")->toArray();
        $idsPosts = Post::query()->get()->pluck("id")->toArray();

        foreach ($idsPosts as $post){
            foreach ($idsUsers as $user){
                PostComment::query()->create([
                    "post_id" => $post,
                    "user_id" => $user,
                    "comment" => $faker->realTextBetween(50,100),
                ]);
            }
        }

    }
}
