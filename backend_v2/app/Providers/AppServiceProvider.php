<?php

namespace App\Providers;

use App\Database\Connectors\NormalizingMySqlConnector;
use Illuminate\Database\Events\ConnectionEstablished;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('db.connector.mysql', function () {
            return new NormalizingMySqlConnector;
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Aiven MySQL enforces sql_require_primary_key; Laravel often creates tables then adds PK in a second statement.
        // PDO::MYSQL_ATTR_INIT_COMMAND caused "Attribute value must be of type bool" on this stack; run once per connection.
        Event::listen(ConnectionEstablished::class, function (ConnectionEstablished $event) {
            $connection = $event->connection;
            if ($connection->getDriverName() !== 'mysql') {
                return;
            }
            $host = (string) ($connection->getConfig('host') ?? '');
            if ($host === '' || ! str_contains($host, 'aivencloud.com')) {
                return;
            }
            $connection->statement('SET SESSION sql_require_primary_key = 0');
        });
    }
}
