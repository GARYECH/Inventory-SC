<?php

namespace Tests\Feature\User;

use App\Http\Controllers\User\DocumentController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionClass;
use Tests\TestCase;

class RentalDurationMinimumTest extends TestCase
{
    /** Invoke the private helper without constructing controller dependencies. */
    private function callDocumentRentalDays(?string $startDate, ?string $endDate): int
    {
        $reflection = new ReflectionClass(DocumentController::class);
        $controller = $reflection->newInstanceWithoutConstructor();

        return $reflection
            ->getMethod('getRentalDays')
            ->invoke($controller, $startDate, $endDate);
    }

    public function test_document_rental_duration_is_at_least_one_day_for_one_day_gap(): void
    {
        $this->assertSame(
            1,
            $this->callDocumentRentalDays('2026-10-18', '2026-10-19')
        );
    }

    public function test_document_rental_duration_is_at_least_one_day_when_dates_are_missing(): void
    {
        $this->assertSame(1, $this->callDocumentRentalDays(null, null));
        $this->assertSame(1, $this->callDocumentRentalDays('2026-10-18', null));
        $this->assertSame(1, $this->callDocumentRentalDays(null, '2026-10-19'));
    }

    public function test_document_rental_duration_does_not_drop_below_one_for_same_or_reversed_dates(): void
    {
        $this->assertSame(
            1,
            $this->callDocumentRentalDays('2026-10-18', '2026-10-18')
        );
        $this->assertSame(
            1,
            $this->callDocumentRentalDays('2026-10-19', '2026-10-18')
        );
    }

    public function test_rental_mou_templates_never_use_zero_day_fallback_or_zero_minimum(): void
    {
        $templates = [
            resource_path('views/admin/pdf/mou_HT.blade.php'),
            resource_path('views/admin/pdf/mou_internal.blade.php'),
            resource_path('views/admin/pdf/mou_vendor.blade.php'),
        ];

        foreach ($templates as $templatePath) {
            $contents = file_get_contents($templatePath);

            $this->assertNotFalse($contents, 'Could not read ' . $templatePath);
            $this->assertStringNotContainsString(
                '{{ $rentalDays ?? 0 }}',
                $contents,
                'MOU must not fall back to displaying zero days: ' . $templatePath
            );
            $this->assertDoesNotMatchRegularExpression(
                '/max\s*\(\s*0\s*,\s*\$dateDifference\s*-\s*1\s*\)/s',
                $contents,
                'MOU rental duration must use a minimum of one day: ' . $templatePath
            );
        }
    }
}
