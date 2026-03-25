<?php

namespace App\Console\Commands;

use App\Models\ApiClient;
use Illuminate\Console\Command;

class ListApiKeys extends Command
{
    protected $signature = 'api:list-keys {--active : Show only active keys}';

    protected $description = 'List all API clients and their keys';

    public function handle()
    {
        $query = ApiClient::query();

        if ($this->option('active')) {
            $query->where('is_active', true);
        }

        $clients = $query->orderBy('created_at', 'desc')->get();

        if ($clients->isEmpty()) {
            $this->info('No API clients found.');
            return Command::SUCCESS;
        }

        $this->table(
            ['ID', 'Name', 'Email', 'Organisation', 'Rate Limit', 'Active', 'Expires', 'Last Used', 'Created'],
            $clients->map(fn($c) => [
                $c->id,
                $c->name,
                $c->email,
                $c->organisation ?? '-',
                $c->rate_limit . '/min',
                $c->is_active ? '✅' : '❌',
                $c->expires_at?->format('Y-m-d') ?? 'Never',
                $c->last_used_at?->diffForHumans() ?? 'Never',
                $c->created_at->format('Y-m-d'),
            ])
        );

        return Command::SUCCESS;
    }
}
