<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\SalesRecord;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SalesDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define realistic products across distinct market segments
        $products = [
            [
                'name' => 'Apex Wireless Noise-Canceling Headphones',
                'category' => 'Audio & Electronics',
                'behavior' => 'strong', // High, stable volume
            ],
            [
                'name' => 'ErgoPro Mechanical Keyboard',
                'category' => 'Computer Peripherals',
                'behavior' => 'moderate', // Dependable, steady volume
            ],
            [
                'name' => 'Precision Stylus Pen Refill Pack',
                'category' => 'Accessories',
                'behavior' => 'weak', // Niche, consistently low volume
            ],
            [
                'name' => 'Nova Smart Fitness Tracker',
                'category' => 'Wearables',
                'behavior' => 'growing', // Upward trajectory over 24 months
            ],
            [
                'name' => 'Legacy USB 2.0 Multi-Hub',
                'category' => 'Computer Peripherals',
                'behavior' => 'declining', // Downward trajectory over 24 months
            ],
            [
                'name' => 'BreezeFlow Desktop Fan',
                'category' => 'Office Essentials',
                'behavior' => 'summer_seasonal', // Strong spike in Jun-Aug
            ],
            [
                'name' => 'Holiday Lumina Desk Lamp Bundle',
                'category' => 'Lighting & Home',
                'behavior' => 'holiday_seasonal', // Strong spike in Nov-Dec
            ],
            [
                'name' => 'VividCapture 4K Streaming Webcam',
                'category' => 'Audio & Electronics',
                'behavior' => 'moderate_high', // Solid 70-100 units with mild Q4 bump
            ],
            [
                'name' => 'ArmorShield Ergonomic Laptop Stand',
                'category' => 'Accessories',
                'behavior' => 'steady_drift', // Mild positive growth from 40 to 65
            ],
            [
                'name' => 'AeroGel Wrist Rest Support',
                'category' => 'Office Essentials',
                'behavior' => 'low_moderate', // Constant 20-30 units
            ],
        ];

        // 24 months of historical data: January 2024 through December 2025
        $startDate = Carbon::create(2024, 1, 1);
        $totalMonths = 24;

        foreach ($products as $prodData) {
            $product = Product::create([
                'name' => $prodData['name'],
                'category' => $prodData['category'],
            ]);

            for ($m = 0; $m < $totalMonths; $m++) {
                $currentMonthDate = (clone $startDate)->addMonths($m);
                $monthOfYear = (int) $currentMonthDate->format('n'); // 1-12

                $quantity = $this->calculateQuantity($prodData['behavior'], $m, $monthOfYear);

                SalesRecord::create([
                    'product_id' => $product->id,
                    'sale_date' => $currentMonthDate->toDateString(),
                    'quantity' => $quantity,
                ]);
            }
        }
    }

    /**
     * Compute realistic unit quantities based on assigned business behavior.
     */
    private function calculateQuantity(string $behavior, int $monthIndex, int $monthOfYear): int
    {
        // monthIndex runs from 0 (Jan 2024) to 23 (Dec 2025)
        return match ($behavior) {
            // Consistently strong: 135 - 170 units with realistic variance
            'strong' => 140 + (($monthIndex * 7) % 25) + (($monthOfYear % 3) * 5),

            // Steady moderate: 55 - 75 units
            'moderate' => 58 + (($monthIndex * 5) % 18) - (($monthIndex % 4) * 2),

            // Consistently weak: 6 - 15 units
            'weak' => 7 + (($monthIndex * 3) % 8),

            // Upward growth: starts ~24 in month 0, climbs to ~155 by month 23
            'growing' => 24 + (int) round($monthIndex * 5.6) + (($monthIndex % 3) * 3),

            // Declining: starts ~92 in month 0, drops to ~16 by month 23
            'declining' => max(12, 92 - (int) round($monthIndex * 3.4) + (($monthIndex % 2) * 4)),

            // Summer seasonal: baseline 18-25, surges in Jun(6), Jul(7), Aug(8) to 110-145
            'summer_seasonal' => match ($monthOfYear) {
                6 => 112 + (($monthIndex % 2) * 10),
                7 => 145 + (($monthIndex % 2) * 8),
                8 => 128 + (($monthIndex % 2) * 6),
                5, 9 => 48 + (($monthIndex % 3) * 5),
                default => 18 + (($monthIndex % 4) * 2),
            },

            // Holiday seasonal: baseline 32-42, spikes in Nov(11) and Dec(12)
            'holiday_seasonal' => match ($monthOfYear) {
                11 => 134 + (($monthIndex % 2) * 14),
                12 => 168 + (($monthIndex % 2) * 12),
                10 => 68 + (($monthIndex % 2) * 6),
                default => 34 + (($monthIndex % 3) * 3),
            },

            // Moderate-high: 70 - 105 units
            'moderate_high' => 74 + (($monthIndex * 4) % 20) + (in_array($monthOfYear, [11, 12]) ? 15 : 0),

            // Steady drift: 40 up to 66
            'steady_drift' => 40 + (int) round($monthIndex * 1.1) + (($monthIndex % 3) * 3),

            // Low-moderate: 20 - 32 units
            'low_moderate' => 22 + (($monthIndex * 2) % 10),

            default => 50,
        };
    }
}
