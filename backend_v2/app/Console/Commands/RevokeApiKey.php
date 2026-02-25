<?php

namespace App\Console\Commands;

use App\Models\ApiClient;
use Illuminate\Console\Command;

class RevokeApiKey extends Command
{
    protected $signature = 'api:revoke-key {id : The API client ID to deactivate}';

    protected $description = 'Deactivate an API key by client ID';

    public function handle()
    {
        $client = ApiClient::find($this->argument('id'));

        if (!$client) {
            $this->error('API client not found.');
            return Command::FAILURE;
        }

        $client->update(['is_active' => false]);

        $this->info("✅ API key for '{$client->name}' ({$client->email}) has been deactivated.");

        return Command::SUCCESS;
    }
}
