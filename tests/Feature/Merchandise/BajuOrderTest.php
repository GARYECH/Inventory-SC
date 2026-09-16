<?php

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(
    RefreshDatabase::class
);


/*
|--------------------------------------------------------------------------
| Render Baju Order Form
|--------------------------------------------------------------------------
*/

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

                /*
                |--------------------------------------------------------------------------
                | ITEM PHOTO
                |--------------------------------------------------------------------------
                */

                'item_photo' =>
                    'test-baju.jpg',

                /*
                |--------------------------------------------------------------------------
                | STOCK
                |--------------------------------------------------------------------------
                */

                'stock_quantity' =>
                    100,

                /*
                |--------------------------------------------------------------------------
                | PRICE
                |--------------------------------------------------------------------------
                */

                'price' =>
                    100000,

                /*
                |--------------------------------------------------------------------------
                | CONDITION
                |--------------------------------------------------------------------------
                */

                'condition_status' =>
                    'Good',
            ]);


        $response =
            $this
                ->actingAs($user)
                ->get(
                    route(
                        'student.cart.baju.create',
                        $item->id
                    )
                );


        $response
            ->assertSuccessful()

            ->assertViewIs(
                'user.baju_order'
            )

            ->assertSee(
                'Size Breakdown'
            )

            ->assertSee(
                'S'
            )

            ->assertSee(
                'M'
            )

            ->assertSee(
                'L'
            )

            ->assertSee(
                'XL'
            )

            ->assertSee(
                '2XL'
            )

            ->assertSee(
                '3XL'
            )

            ->assertSee(
                '4XL'
            )

            ->assertSee(
                '5XL'
            )

            ->assertSee(
                'Baju Panitia'
            );
    }
);