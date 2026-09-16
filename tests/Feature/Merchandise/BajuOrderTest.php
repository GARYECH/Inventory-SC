<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(
    RefreshDatabase::class
);

it(
    'renders the baju order form',
    function () {

        $user =
            User::factory()->create();

        $category =
            Category::create([
                'name' =>
                    'Merchandise',

                'slug' =>
                    'merchandise',
            ]);

        $item =
            Item::create([
                'name' =>
                    'Baju Panitia',

                'category_id' =>
                    $category->id,

                'transaction_type' =>
                    'Merchandise',

                'transaction_detail' =>
                    null,

                'subcategory' =>
                    'Baju',

                'requires_mou' =>
                    true,

                'description' =>
                    'Baju Panitia',

                'item_photo' =>
                    null,

                'stock_quantity' =>
                    100,

                'price' =>
                    100000,

                'condition_status' =>
                    'Good',
            ]);

        $response =
            $this
                ->actingAs($user)
                ->get(
                    route(
                        'student.baju.create',
                        $item->id
                    )
                );

        $response
            ->assertSuccessful()
            ->assertViewIs(
                'user.baju_order'
            )
            ->assertSee('Size Breakdown')
            ->assertSee('2XL')
            ->assertSee('5XL');
    }
);