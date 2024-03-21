<?php

namespace App\Providers;

use App\Models\Request;
use App\Observers\RequestObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;

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
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Request::observe(RequestObserver::class);

        Event::listen(BuildingMenu::class, function (BuildingMenu $event){
            $event->menu->addAfter('request',[
                'text' => 'Roles',
                'route' => 'role.index',
                'icon' => 'fa fa-id-badge',
                'key'  => 'roles',
                'can'  => 'view role'
            ]);
            $event->menu->addAfter('roles',[
                'text' => 'Permissions',
                'route' => 'permission.index',
                'icon' => 'fa fa-check-circle',
                'key'  => 'permissions',
                'can'  => 'view permission'
            ]);
        });
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
