<?php

namespace App\Providers;

use App\Events\BonusAktivasiPin;
use App\Events\BonusCashback;
use App\Events\BonusGenerasi;
use App\Events\BonusReward;
use App\Events\PembelianDetailAktivasi;
use App\Events\SpillOverBonusBulanan;
use App\Events\UserCreated;
use App\Listeners\BonusAktivasiPinListener;
use App\Listeners\BonusCashbackListener;
use App\Listeners\BonusGenerasiListener;
use App\Listeners\BonusRewardListener;
use App\Listeners\CreateJaringanMitra;
use App\Listeners\PembelianDetailAktivasiListener;
use App\Listeners\SpillOverBonusBulananListener;
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
        BonusAktivasiPin::class => [
            BonusAktivasiPinListener::class,
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
        \App\Events\ChangeLevelUser::class => [
            \App\Listeners\ChangeLevelUserListener::class,
        ],
        UserCreated::class => [
            CreateJaringanMitra::class,
        ],
        PembelianDetailAktivasi::class => [
            PembelianDetailAktivasiListener::class,
        ],
        SpillOverBonusBulanan::class => [
            SpillOverBonusBulananListener::class,
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
