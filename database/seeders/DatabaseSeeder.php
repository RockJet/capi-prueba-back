<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Phone;
use App\Models\Email;
use App\Models\Address;
use Illuminate\Support\Facades\Schema;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        Schema::disableForeignKeyConstraints();
        Contact::truncate();
        Phone::truncate();
        Email::truncate();
        Address::truncate();
        Schema::enableForeignKeyConstraints();

        Contact::factory()->count(5000)->create()->each(function ($contact) {
            $contact->phones()->saveMany(Phone::factory()->count(2)->make());
            $contact->emails()->saveMany(Email::factory()->count(2)->make());
            $contact->addresses()->saveMany(Address::factory()->count(2)->make());
        });
    }
}
