<?php

namespace Database\Seeders;

use App\Messanger;
use App\photo;
use App\Post;
use App\React;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            $email = strtolower(trim((string) config('admin.initial_email')));
            $password = (string) config('admin.initial_password');
            $name = trim((string) config('admin.initial_name', 'Site Administrator'));

            if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 16 || $name === '') {
                throw new InvalidArgumentException('Production seeding requires INITIAL_ADMIN_EMAIL, INITIAL_ADMIN_NAME, and an INITIAL_ADMIN_PASSWORD of at least 16 characters.');
            }

            [$firstName, $lastName] = array_pad(explode(' ', $name, 2), 2, '');
            $existingAdmin = User::where('email', $email)->first();
            if ($existingAdmin && (!$existingAdmin->is_admin || !$existingAdmin->is_active)) {
                throw new InvalidArgumentException('INITIAL_ADMIN_EMAIL already belongs to an account that is not an active administrator. Choose an unused email or correct the account through an authorized administrator.');
            }

            User::firstOrCreate(
                ['email' => $email],
                [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'password' => Hash::make($password),
                    'email_verified_at' => now(),
                    'status' => 1,
                    'is_admin' => true,
                    'is_active' => true,
                ]
            );

            return;
        }

        $demo = User::updateOrCreate(
            ['email' => 'demo@example.com'],
            ['first_name' => 'Demo', 'last_name' => 'User', 'password' => bcrypt('Demo@12345'), 'status' => 1, 'is_admin' => true, 'is_active' => true]
        );
        $demo->forceFill(['email_verified_at' => $demo->email_verified_at ?? now()])->save();

        $demoPhoto = photo::updateOrCreate(
            ['id' => 1],
            ['user_id' => $demo->id, 'path' => 'images/avatars/', 'type' => '.svg', 'state' => 1, 'album_id' => 0]
        );
        $demo->update(['profile_photo_id' => $demoPhoto->id]);

        $contacts = [
            ['Mohamed', 'Hassan', 'mohamed@example.com'],
            ['Sarah', 'Ahmed', 'sarah@example.com'],
            ['Omar', 'Gamal', 'omar@example.com'],
            ['Mahmoud', 'Ali', 'mahmoud@example.com'],
            ['Nour', 'Mohamed', 'nour@example.com'],
        ];

        $contactUsers = [];
        foreach ($contacts as $index => [$first, $last, $email]) {
            $user = User::updateOrCreate(
                ['email' => $email],
                ['first_name' => $first, 'last_name' => $last, 'password' => bcrypt('Demo@12345'), 'status' => 1]
            );
            $user->forceFill(['email_verified_at' => $user->email_verified_at ?? now()])->save();
            $photoId = $index + 2;
            $avatar = photo::updateOrCreate(
                ['id' => $photoId],
                ['user_id' => $user->id, 'path' => 'images/avatars/', 'type' => '.svg', 'state' => 1, 'album_id' => 0]
            );
            $user->update(['profile_photo_id' => $avatar->id]);
            $contactUsers[] = $user;
            Messanger::firstOrCreate(['my_id' => $demo->id, 'user_id' => $user->id], ['message' => "مرحباً {$first}!", 'read' => 0]);
            Messanger::firstOrCreate(['my_id' => $user->id, 'user_id' => $demo->id], ['message' => 'أهلاً بك، سعيد بالتواصل معك.', 'read' => 0]);
        }

        $welcomePost = Post::firstOrCreate(['user_id' => $demo->id, 'post_text' => 'Welcome to the social network!'], ['status' => 1]);
        $dayPost = Post::firstOrCreate(['user_id' => $demo->id, 'post_text' => 'A beautiful day to share ideas with friends.'], ['status' => 1]);

        foreach ($contactUsers as $index => $contact) {
            React::updateOrCreate(
                ['post_id' => $welcomePost->id, 'user_id' => $contact->id],
                ['type' => ($index % 7) + 1]
            );
        }
        React::updateOrCreate(['post_id' => $dayPost->id, 'user_id' => $contactUsers[0]->id], ['type' => 2]);
    }
}
