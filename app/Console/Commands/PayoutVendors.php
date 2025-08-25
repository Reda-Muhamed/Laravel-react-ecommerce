<?php

namespace App\Console\Commands;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Models\Payout;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PayoutVendors extends Command
{
        protected $signature = 'payout:vendors';


    protected $description = 'Perform payouts to vendors' ;


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting monthly payouts to vendors...');
        $vendors = Vendor::eligibleForPayout()->get();
        $vendors = $vendors->filter(fn($vendor) => $vendor->user->isStripeAccountActive());

        foreach($vendors as $vendor){
            $this->processPayout($vendor);
        }
        $this->info('Monthly payouts completed.');
        return Command::SUCCESS;
    }
    protected function processPayout(Vendor $vendor)
    {
        $this->info("Processing payout for vendor: {$vendor->store_name} (User ID: {$vendor->user_id})");
        try {
            DB::beginTransaction();
            $startingFrom = Payout::where('vendor_id',$vendor->user_id)->orderBy('until','desc')->value('until');
            $startingFrom = $startingFrom ?:Carbon::make('1970-01-01');
            $until = Carbon::now()->subMonthNoOverflow()->startOfMonth();
            $vendorSubtotal = Order::query()
                ->where('vendor_user_id', $vendor->user_id)
                ->where('status', OrderStatusEnum::Paid->value)
                ->whereBetween('created_at', [$startingFrom, $until])
                ->sum('vendor_subtotal');
            if($vendorSubtotal) {
                $this->info("Calculated vendor subtotal: $vendorSubtotal from $startingFrom to $until");
                Payout::create([
                    'vendor_id' => $vendor->user_id,
                    'amount' => $vendorSubtotal,
                    'starting_from' => $startingFrom,
                    'until' => $until,
                ]);
                // we should implement this function
                $vendor->user->transfer((int)($vendorSubtotal * 100),
                config('app.currency'));
                $this->info("Payout of $vendorSubtotal created for vendor {$vendor->store_name} from $startingFrom to $until.");

            }else{
                $this->info("No sales for vendor {$vendor->store_name} from $startingFrom to $until. Skipping payout.");
            }
            DB::commit();

        } catch (\Throwable $th) {
            DB::rollBack();
            $this->error($th->getMessage());
            //throw $th;
        }

    }
}
