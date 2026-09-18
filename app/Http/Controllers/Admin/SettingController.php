<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display system settings.
     */
    public function index()
    {
        $settings =
            Setting::pluck(
                'value',
                'key'
            )->toArray();

        return view(
            'admin.settings.index',
            compact(
                'settings'
            )
        );
    }

    /**
     * Update system settings.
     */
    public function update(
        Request $request
    ) {
        /*
        |--------------------------------------------------------------------------
        | MOU INTERNAL
        |--------------------------------------------------------------------------
        */

        if (
            $request->has('mou_internal')
        ) {
            Setting::updateOrCreate(
                [
                    'key' =>
                        'mou_internal',
                ],
                [
                    'value' =>
                        $request->input(
                            'mou_internal'
                        ),
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | MOU VENDOR
        |--------------------------------------------------------------------------
        */

        if (
            $request->has('mou_vendor')
        ) {
            Setting::updateOrCreate(
                [
                    'key' =>
                        'mou_vendor',
                ],
                [
                    'value' =>
                        $request->input(
                            'mou_vendor'
                        ),
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | LOGO SC
        |--------------------------------------------------------------------------
        */

        if (
            $request->hasFile(
                'logo_sc'
            )
        ) {
            $request->validate([
                'logo_sc' => [
                    'image',
                    'mimes:png,jpg,jpeg',
                    'max:2048',
                ],
            ]);

            $path =
                $request
                    ->file('logo_sc')
                    ->storeAs(
                        'images',
                        'logo_sc.png',
                        'public'
                    );

            Setting::updateOrCreate(
                [
                    'key' =>
                        'logo_sc',
                ],
                [
                    'value' =>
                        $path,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | TTD BENDAHARA
        |--------------------------------------------------------------------------
        */

        if (
            $request->hasFile(
                'ttd_bendahara'
            )
        ) {
            $request->validate([
                'ttd_bendahara' => [
                    'image',
                    'mimes:png,jpg,jpeg',
                    'max:2048',
                ],
            ]);

            $path =
                $request
                    ->file('ttd_bendahara')
                    ->storeAs(
                        'images',
                        'ttd_bendahara.png',
                        'public'
                    );

            Setting::updateOrCreate(
                [
                    'key' =>
                        'ttd_bendahara',
                ],
                [
                    'value' =>
                        $path,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | COLOR CHART BAJU
        |--------------------------------------------------------------------------
        */

        if (
            $request->hasFile(
                'color_charts'
            )
        ) {
            $request->validate([
                'color_charts' => [
                    'array',
                    'max:20',
                ],

                'color_charts.*' => [
                    'image',
                    'mimes:png,jpg,jpeg,webp',
                    'max:5120',
                ],
            ]);

            $existingColorCharts =
                Setting::where(
                    'key',
                    'baju_color_charts'
                )->value(
                    'value'
                );

            $existingColorCharts =
                $existingColorCharts
                    ? json_decode(
                        $existingColorCharts,
                        true
                    )
                    : [];

            if (
                !is_array(
                    $existingColorCharts
                )
            ) {
                $existingColorCharts = [];
            }

            foreach (
                $request->file(
                    'color_charts'
                ) as $file
            ) {
                $path =
                    $file->store(
                        'images/color-charts',
                        'public'
                    );

                $existingColorCharts[] =
                    $path;
            }

            Setting::updateOrCreate(
                [
                    'key' =>
                        'baju_color_charts',
                ],
                [
                    'value' =>
                        json_encode(
                            array_values(
                                $existingColorCharts
                            )
                        ),
                ]
            );
        }

        return back()->with(
            'success',
            'System Settings berhasil diperbarui!'
        );
    }
}