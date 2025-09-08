<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class StatsOverview extends ChartWidget
{
    protected static ?string $heading = 'Orders (Last 7 Days)';

    protected function getData(): array
    {
        $ordersQuery = Order::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as total'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date');

        if (auth()->user()->hasRole(\App\Enums\RolesEnum::Vendor->value)) {
            // dd(auth()->user()->vendor->user_id);

            $ordersQuery->where('vendor_user_id', auth()->user()->vendor->user_id);
        }

        $orders = $ordersQuery->pluck('total', 'date');

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => $orders->values(),
                    'borderColor' => '#4F46E5',
                    'backgroundColor' => 'rgba(79,70,229,0.2)',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $orders->keys()->toArray(),
        ];
    }


    protected function getType(): string
    {
        return 'line';
    }
}
