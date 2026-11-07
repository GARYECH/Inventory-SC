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
| Helper
|--------------------------------------------------------------------------
*/

function createBajuItem(): Item
{
    $category =
        Category::create([
            'name' =>
                'Merchandise',

            'slug' =>
                'merchandise',
        ]);


    return Item::create([
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
            'test-baju.jpg',

        'stock_quantity' =>
            100,

        'price' =>
            100000,

        'condition_status' =>
            'Good',
    ]);
}


/*
|--------------------------------------------------------------------------
| Mixed Merchandise Cart
|--------------------------------------------------------------------------
*/

it(
    'keeps ID Card and Baju in one merchandise cart with the same pickup schedule',
    function () {

        $user =
            User::factory()->create();


        $bajuItem =
            createBajuItem();


        $idCardItem =
            Item::create([
                'name' =>
                    'ID Card Panitia',

                'category_id' =>
                    $bajuItem->category_id,

                'transaction_type' =>
                    'Merchandise',

                'transaction_detail' =>
                    null,

                'subcategory' =>
                    'ID Card',

                'requires_mou' =>
                    true,

                'description' =>
                    'ID Card Panitia',

                'item_photo' =>
                    'test-id-card.jpg',

                'stock_quantity' =>
                    100,

                'price' =>
                    15000,

                'condition_status' =>
                    'Good',
            ]);


        $startDate =
            now()
                ->addDay()
                ->format('Y-m-d');


        $existingBajuCart = [
            'existing-baju' => [
                'id' =>
                    $bajuItem->id,

                'name' =>
                    $bajuItem->name,

                'price' =>
                    100000,

                'quantity' =>
                    1,

                'transaction_type' =>
                    'Merchandise',

                'transaction_detail' =>
                    null,

                'subcategory' =>
                    'Baju',

                'start_date' =>
                    $startDate,

                'start_time' =>
                    '17:30',

                'end_date' =>
                    null,

                'end_time' =>
                    null,

                'size_breakdowns' => [
                    [
                        'size' =>
                            'L',

                        'division' =>
                            'Event',

                        'quantity' =>
                            1,

                        'unit_price' =>
                            100000,

                        'size_additional_price' =>
                            0,

                        'subtotal_price' =>
                            100000,
                    ],
                ],

                'color_number' =>
                    '01',

                'design_link' =>
                    'https://drive.google.com/baju',

                'requires_mou' =>
                    true,
            ],
        ];


        /*
        |----------------------------------------------------------------------
        | DASHBOARD PREFILLS THE EXISTING SCHEDULE
        |----------------------------------------------------------------------
        */

        $dashboardResponse =
            $this
                ->actingAs($user)
                ->withSession([
                    'cart' =>
                        $existingBajuCart,
                ])
                ->get(
                    route('student.dashboard')
                );


        $dashboardResponse
            ->assertSuccessful()
            ->assertSee(
                'data-existing-start-date="' . $startDate . '"',
                false
            )
            ->assertSee(
                'data-existing-start-time="17:30"',
                false
            );


        /*
        |----------------------------------------------------------------------
        | ADD ID CARD TO THE SAME MERCHANDISE CART
        |----------------------------------------------------------------------
        */

        $addResponse =
            $this->post(
                route(
                    'student.cart.add',
                    $idCardItem->id
                ),
                [
                    'quantity' =>
                        2,

                    'start_date' =>
                        $startDate,

                    'start_time' =>
                        '17:30',

                    'design_link' =>
                        'https://drive.google.com/id-card',
                ]
            );


        $addResponse
            ->assertSessionHasNoErrors();


        $updatedCart =
            session('cart');


        expect($updatedCart)
            ->toBeArray()
            ->toHaveCount(2);


        expect(
            collect($updatedCart)
                ->pluck('subcategory')
                ->contains('ID Card')
        )->toBeTrue();


        expect(
            collect($updatedCart)
                ->every(
                    fn ($cartItem) =>
                        ($cartItem['transaction_type'] ?? null) === 'Merchandise'
                )
        )->toBeTrue();

    }
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


        $item =
            createBajuItem();


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
                'Rincian ukuran'
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
                '10XL'
            )

            ->assertSee(
                'Baju Panitia'
            );
    }
);


/*
|--------------------------------------------------------------------------
| Duplicate Size Is Stored Separately
|--------------------------------------------------------------------------
*/

it(
    'stores duplicate sizes as separate size breakdown rows',
    function () {

        $user =
            User::factory()->create();


        $item =
            createBajuItem();


        $startDate =
            now()
                ->addDay()
                ->format('Y-m-d');


        $response =
            $this
                ->actingAs($user)
                ->post(
                    route(
                        'student.cart.baju.store',
                        $item->id
                    ),
                    [
                        'start_date' =>
                            $startDate,

                        'start_time' =>
                            '17:00',

                        'design_link' =>
                            'https://drive.google.com/example',

                        'sizes' => [

                            [
                                'size' =>
                                    'L',

                                'division' =>
                                    'Event',

                                'quantity' =>
                                    5,
                            ],

                            [
                                'size' =>
                                    'L',

                                'division' =>
                                    'SC',

                                'quantity' =>
                                    3,
                            ],
                        ],
                    ]
                );


        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

        $response
            ->assertRedirect(
                route(
                    'student.cart.index'
                )
            );


        /*
        |--------------------------------------------------------------------------
        | SESSION CART
        |--------------------------------------------------------------------------
        */

        $cart =
            session(
                'cart'
            );


        expect($cart)
            ->toBeArray()
            ->not->toBeEmpty();


        /*
        |--------------------------------------------------------------------------
        | FIND BAJU LINE
        |--------------------------------------------------------------------------
        */

        $bajuLine =
            collect(
                $cart
            )->first(
                function (
                    $cartItem
                ) use ($item) {

                    return (int) (
                        $cartItem['id'] ?? 0
                    )
                    ===
                    (int) $item->id

                    &&
                    (
                        $cartItem[
                            'transaction_type'
                        ] ?? null
                    )
                    ===
                    'Merchandise'

                    &&
                    (
                        $cartItem[
                            'subcategory'
                        ] ?? null
                    )
                    ===
                    'Baju';

                }
            );


        expect($bajuLine)
            ->not->toBeNull();


        /*
        |--------------------------------------------------------------------------
        | BREAKDOWNS
        |--------------------------------------------------------------------------
        */

        $breakdowns =
            $bajuLine[
                'size_breakdowns'
            ] ?? [];


        expect($breakdowns)
            ->toHaveCount(2);


        /*
        |--------------------------------------------------------------------------
        | FIRST L
        |--------------------------------------------------------------------------
        */

        expect($breakdowns[0])
            ->toMatchArray([
                'size' =>
                    'L',

                'division' =>
                    'Event',

                'quantity' =>
                    5,

                'unit_price' =>
                    100000,

                'size_additional_price' =>
                    0,

                'subtotal_price' =>
                    500000,
            ]);


        /*
        |--------------------------------------------------------------------------
        | SECOND L
        |--------------------------------------------------------------------------
        */

        expect($breakdowns[1])
            ->toMatchArray([
                'size' =>
                    'L',

                'division' =>
                    'SC',

                'quantity' =>
                    3,

                'unit_price' =>
                    100000,

                'size_additional_price' =>
                    0,

                'subtotal_price' =>
                    300000,
            ]);


        /*
        |--------------------------------------------------------------------------
        | TOTAL QUANTITY
        |--------------------------------------------------------------------------
        */

        expect(
            $bajuLine[
                'quantity'
            ]
        )
            ->toBe(8);


        /*
        |--------------------------------------------------------------------------
        | COLOR
        |--------------------------------------------------------------------------
        */

        expect(
            $bajuLine[
                'color_number'
            ] ?? null
        )
            ->toBeNull();


        /*
        |--------------------------------------------------------------------------
        | DESIGN
        |--------------------------------------------------------------------------
        */

        expect(
            $bajuLine[
                'design_link'
            ] ?? null
        )
            ->toBe(
                'https://drive.google.com/example'
            );

    }
);


/*
|--------------------------------------------------------------------------
| Size Validation
|--------------------------------------------------------------------------
*/

it(
    'requires size breakdown fields',
    function () {

        $user =
            User::factory()->create();


        $item =
            createBajuItem();


        $startDate =
            now()
                ->addDay()
                ->format('Y-m-d');


        $response =
            $this
                ->actingAs($user)
                ->post(
                    route(
                        'student.cart.baju.store',
                        $item->id
                    ),
                    [
                        'start_date' =>
                            $startDate,

                        'start_time' =>
                            '17:00',

                        'design_link' =>
                            'https://drive.google.com/example',

                        'sizes' => [

                            [
                                'size' =>
                                    'L',

                                'division' =>
                                    '',

                                'quantity' =>
                                    0,
                            ],
                        ],
                    ]
                );


        $response
            ->assertSessionHasErrors([
                'sizes.0.division',
                'sizes.0.quantity',
            ]);
    }
);


/*
|--------------------------------------------------------------------------
| Multiple Different Size Rows
|--------------------------------------------------------------------------
*/

it(
    'accepts multiple size breakdown rows',
    function () {

        $user =
            User::factory()->create();


        $item =
            createBajuItem();


        $startDate =
            now()
                ->addDay()
                ->format('Y-m-d');


        $response =
            $this
                ->actingAs($user)
                ->post(
                    route(
                        'student.cart.baju.store',
                        $item->id
                    ),
                    [
                        'start_date' =>
                            $startDate,

                        'start_time' =>
                            '18:00',

                        'design_link' =>
                            'https://drive.google.com/example',

                        'sizes' => [

                            [
                                'size' =>
                                    'S',

                                'division' =>
                                    'Event',

                                'quantity' =>
                                    5,
                            ],

                            [
                                'size' =>
                                    'M',

                                'division' =>
                                    'SC',

                                'quantity' =>
                                    10,
                            ],

                            [
                                'size' =>
                                    'XL',

                                'division' =>
                                    'BPH',

                                'quantity' =>
                                    2,
                            ],
                        ],
                    ]
                );


        $response
            ->assertRedirect(
                route(
                    'student.cart.index'
                )
            );


        $cart =
            session(
                'cart'
            );


        $bajuLine =
            collect(
                $cart
            )->first(
                function (
                    $cartItem
                ) use ($item) {

                    return (int) (
                        $cartItem['id'] ?? 0
                    )
                    ===
                    (int) $item->id;

                }
            );


        expect(
            $bajuLine[
                'size_breakdowns'
            ]
        )
            ->toHaveCount(3);


        expect(
            $bajuLine[
                'quantity'
            ]
        )
            ->toBe(17);

    }
    
);
/*
|--------------------------------------------------------------------------
| Checkout Stores Duplicate Size Rows
|--------------------------------------------------------------------------
*/

it(
    'stores duplicate baju sizes as separate order item size rows during checkout',
    function () {

        $user =
            User::factory()->create();


        $item =
            createBajuItem();


        $startDate =
            now()
                ->addDay()
                ->format('Y-m-d');


        /*
        |--------------------------------------------------------------------------
        | PUT BAJU INTO CART
        |--------------------------------------------------------------------------
        */

        $this
            ->actingAs($user)
            ->post(
                route(
                    'student.cart.baju.store',
                    $item->id
                ),
                [
                    'start_date' =>
                        $startDate,

                    'start_time' =>
                        '17:00',

                    'design_link' =>
                        'https://drive.google.com/example',

                    'sizes' => [

                        [
                            'size' =>
                                'L',

                            'division' =>
                                'Event',

                            'quantity' =>
                                5,
                        ],

                        [
                            'size' =>
                                'L',

                            'division' =>
                                'SC',

                            'quantity' =>
                                3,
                        ],
                    ],
                ]
            );


        /*
        |--------------------------------------------------------------------------
        | SET COLOR
        |--------------------------------------------------------------------------
        */

        $this
            ->actingAs($user)
            ->patch(
                route(
                    'student.cart.color',
                    $item->id
                ),
                [
                    'color_number' =>
                        '47 Navy',
                ]
            );


        /*
        |--------------------------------------------------------------------------
        | CHECKOUT
        |--------------------------------------------------------------------------
        */

        $response =
            $this
                ->actingAs($user)
                ->post(
                    route(
                        'student.cart.checkout'
                    ),
                    [
                        'full_name' =>
                            'Gregory Christian',

                        'organization' =>
                            'Student Council',

                        'position' =>
                            'Bendahara',

                        'phone_number' =>
                            '08123456789',

                        'proker_name' =>
                            'Test Event',

                        'ketua_acara' =>
                            'Test Ketua',

                        'treasurer_name' =>
                            'Test Bendahara',

                        'address' =>
                            'Universitas Ciputra Surabaya',

                        'notes' =>
                            'Testing duplicate size.',
                        'funding_source' => 'program_treasurer',

                    ]
                );


        /*
        |--------------------------------------------------------------------------
        | CHECKOUT REDIRECT
        |--------------------------------------------------------------------------
        */

        $response
            ->assertRedirect(
                route(
                    'student.loans'
                )
            );


        /*
        |--------------------------------------------------------------------------
        | ORDER
        |--------------------------------------------------------------------------
        */

        $order =
            \App\Models\Order::latest(
                'id'
            )->first();


        expect($order)
            ->not->toBeNull();


        expect($order->order_type)
            ->toBe('Merchandise');


        expect($order->status)
            ->toBe('Pending');


        /*
        |--------------------------------------------------------------------------
        | ORDER ITEM
        |--------------------------------------------------------------------------
        */

        $orderItem =
            $order
                ->orderItems()
                ->where(
                    'item_id',
                    $item->id
                )
                ->first();


        expect($orderItem)
            ->not->toBeNull();


        expect($orderItem->quantity)
            ->toBe(8);


        expect($orderItem->color_number)
            ->toBe('47 Navy');


        expect($orderItem->design_link)
            ->toBe(
                'https://drive.google.com/example'
            );


        expect($orderItem->subtotal_price)
            ->toBe(800000);


        /*
        |--------------------------------------------------------------------------
        | ORDER ITEM SIZE BREAKDOWNS
        |--------------------------------------------------------------------------
        */

        $sizeRows =
            $orderItem
                ->sizeBreakdowns()
                ->orderBy(
                    'id'
                )
                ->get();


        expect($sizeRows)
            ->toHaveCount(2);


        /*
        |--------------------------------------------------------------------------
        | FIRST L
        |--------------------------------------------------------------------------
        */

        expect($sizeRows[0])
            ->toMatchArray([
                'size' =>
                    'L',

                'division' =>
                    'Event',

                'quantity' =>
                    5,

                'unit_price' =>
                    100000,

                'size_additional_price' =>
                    0,

                'subtotal_price' =>
                    500000,
            ]);


        /*
        |--------------------------------------------------------------------------
        | SECOND L
        |--------------------------------------------------------------------------
        */

        expect($sizeRows[1])
            ->toMatchArray([
                'size' =>
                    'L',

                'division' =>
                    'SC',

                'quantity' =>
                    3,

                'unit_price' =>
                    100000,

                'size_additional_price' =>
                    0,

                'subtotal_price' =>
                    300000,
            ]);


        /*
        |--------------------------------------------------------------------------
        | PHYSICAL STOCK SHOULD NOT DECREASE AT CHECKOUT
        |--------------------------------------------------------------------------
        */

        expect(
            $item->fresh()->stock_quantity
        )
            ->toBe(100);

    }
);
