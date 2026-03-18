<?php

namespace Database\Seeders;

use App\Models\Content;
use App\Models\Event;
use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Create Tags
        $tags = [
            ['name' => 'Solo', 'slug' => 'solo'],
            ['name' => 'Group', 'slug' => 'group'],
            ['name' => 'Candid', 'slug' => 'candid'],
            ['name' => 'Full Member', 'slug' => 'full-member'],
            ['name' => 'Special Outfit', 'slug' => 'special-outfit'],
            ['name' => 'Backstage', 'slug' => 'backstage'],
        ];

        $tagModels = [];
        foreach ($tags as $tag) {
            $tagModels[] = Tag::updateOrCreate(['slug' => $tag['slug']], $tag);
        }

        // 2. Create Events
        $events = [
            [
                'name' => 'Tokyo Idol Festival 2024',
                'slug' => 'tokyo-idol-festival-2024',
                'date' => '2024-08-02',
                'location' => 'Odaiba, Tokyo',
                'description' => 'The world\'s largest idol festival held annually in Tokyo.'
            ],
            [
                'name' => 'JKT48 12th Anniversary Concert',
                'slug' => 'jkt48-12th-anniversary',
                'date' => '2023-12-17',
                'location' => 'JIExpo, Jakarta',
                'description' => 'Celebrating twelve years of the first overseas AKB48 sister group.'
            ],
            [
                'name' => 'SNH48 Group Summer Concert 2024',
                'slug' => 'snh48-summer-concert-2024',
                'date' => '2024-07-20',
                'location' => 'Shanghai, China',
                'description' => 'The annual summer gathering for all SNH48 Group members.'
            ],
        ];

        $eventModels = [];
        foreach ($events as $event) {
            $eventModels[] = Event::updateOrCreate(['slug' => $event['slug']], $event);
        }

        // 3. Create Contents
        $contentsData = [
            [
                'title' => 'Minami-chan on Stage',
                'type' => 'image',
                'event_index' => 0,
                'status' => 'active',
                'google_drive_url' => 'https://images.unsplash.com/photo-1514525253361-bee8d6744a7e?q=80&w=2000&auto=format&fit=crop',
                'tags' => [0, 2]
            ],
            [
                'title' => 'Performance Highlights',
                'type' => 'video',
                'event_index' => 1,
                'status' => 'active',
                'google_drive_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', // Dummy
                'tags' => [1, 3]
            ],
            [
                'title' => 'Summer Night Magic',
                'type' => 'image',
                'event_index' => 2,
                'status' => 'active',
                'google_drive_url' => 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?q=80&w=2000&auto=format&fit=crop',
                'tags' => [1, 4]
            ],
            [
                'title' => 'Theater Show Candid',
                'type' => 'image',
                'event_index' => 1,
                'status' => 'active',
                'google_drive_url' => 'https://images.unsplash.com/photo-1524368278786-fbdf2cb37a4c?q=80&w=2000&auto=format&fit=crop',
                'tags' => [0, 2, 5]
            ],
            [
                'title' => 'Festival Crowd Vibes',
                'type' => 'image',
                'event_index' => 0,
                'status' => 'active',
                'google_drive_url' => 'https://images.unsplash.com/photo-1459749411177-042180ce673c?q=80&w=2000&auto=format&fit=crop',
                'tags' => [1]
            ],
            [
                'title' => 'Golden Hour Performance',
                'type' => 'image',
                'event_index' => 2,
                'status' => 'active',
                'google_drive_url' => 'https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?q=80&w=2000&auto=format&fit=crop',
                'tags' => [0, 4]
            ],
            [
                'title' => 'Vocal Duo Focus',
                'type' => 'video',
                'event_index' => 0,
                'status' => 'active',
                'google_drive_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', // Dummy
                'tags' => [0, 3]
            ],
            [
                'title' => 'Anniversary Finale',
                'type' => 'image',
                'event_index' => 1,
                'status' => 'active',
                'google_drive_url' => 'https://images.unsplash.com/photo-1470225620780-dba8ba36b745?q=80&w=2000&auto=format&fit=crop',
                'tags' => [1, 3, 4]
            ],
        ];

        foreach ($contentsData as $c) {
            $content = Content::firstOrCreate(
                ['title' => $c['title'], 'event_id' => $eventModels[$c['event_index']]->id],
                [
                    'type' => $c['type'],
                    'status' => $c['status'],
                    'google_drive_url' => $c['google_drive_url'],
                    'view_count' => rand(100, 5000),
                ]
            );

            // Sync tags
            $tagIds = [];
            foreach ($c['tags'] as $idx) {
                $tagIds[] = $tagModels[$idx]->id;
            }
            $content->tags()->sync($tagIds);
        }
    }
}
