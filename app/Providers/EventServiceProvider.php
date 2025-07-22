<?php

namespace App\Providers;

use App\Events\BonusCashback;
use App\Events\BonusGenerasi;
use App\Events\BonusReward;
use App\Listeners\BonusCashbackListener;
use App\Listeners\BonusGenerasiListener;
use App\Listeners\BonusRewardListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        BonusGenerasi::class => [
            BonusGenerasiListener::class,
        ],
        \App\Events\BonusSponsor::class => [
            \App\Listeners\BonusSponsorListener::class,
        ],
        BonusCashback::class => [
            BonusCashbackListener::class,
        ],
        BonusReward::class => [
            BonusRewardListener::class,
        ],
        \App\Events\PembelianDiterima::class => [
            \App\Listeners\ProsesPembelianDiterima::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
