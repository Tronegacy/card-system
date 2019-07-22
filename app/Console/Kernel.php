<?php
namespace App\Console; use App\System; use Carbon\Carbon; use Illuminate\Console\Scheduling\Schedule; use Illuminate\Foundation\Console\Kernel as ConsoleKernel; use Illuminate\Support\Facades\DB; use Illuminate\Support\Facades\Log; use Illuminate\Support\Facades\Schema; class Kernel extends ConsoleKernel { protected $commands = array(); protected function schedule(Schedule $sp04c765) { if (!app()->runningInConsole()) { return; } try { System::_init(); } catch (\Throwable $sp3f4aab) { return; } if (System::_getInt('order_clean_unpay_open') === 1) { $sp403e35 = System::_getInt('order_clean_unpay_day', 7); $sp04c765->call(function () use($sp403e35) { echo '[' . date('Y-m-d H:i:s') . "] cleaning unpaid orders({$sp403e35} days ago)...\n"; \App\Order::where('status', \App\Order::STATUS_UNPAY)->where('created_at', '<', (new Carbon())->addDays(-$sp403e35))->delete(); $spe95830 = '[' . date('Y-m-d H:i:s') . '] unpaid-orders cleaned 
'; echo $spe95830; })->dailyAt('01:00'); } $sp04c765->call(function () { $sp403e35 = 7; echo '[' . date('Y-m-d H:i:s') . "] cleaning deleted cards({$sp403e35} days ago)...\n"; \App\Card::onlyTrashed()->where('deleted_at', '<', (new Carbon())->addDays(-$sp403e35))->forceDelete(); $spe95830 = '[' . date('Y-m-d H:i:s') . '] deleted-cards cleaned
'; echo $spe95830; })->dailyAt('02:00'); } protected function commands() { $this->load(__DIR__ . '/Commands'); require base_path('routes/console.php'); } }