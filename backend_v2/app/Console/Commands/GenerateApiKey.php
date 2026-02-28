<?php

namespace App\Console\Commands;

use App\Models\ApiClient;
use Illuminate\Console\Command;

class GenerateApiKey extends Command
{
    protected $signature = 'api:generate-key
                            {name : Client/application name}
                            {email : Client email address}
                            {--organisation= : Organisation name}
                            {--rate-limit=60 : Requests per minute}
                            {--expires= : Expiry date (Y-m-d format)}
                            {--description= : Description of the API key usage}';

    protected $description = 'Generate a new API key for an external client';

    public function handle()
    {
        $plainKey = ApiClient::generateApiKey();

        $client = ApiClient::create([
            'name' => $this->argument('name'),
            'email' => $this->argument('email'),
            'organisation' => $this->option('organisation'),
            'api_key' => substr($plainKey, 0, 8) . '...' . substr($plainKey, -4), // masked for display
            'api_key_hash' => ApiClient::hashApiKey($plainKey),
            'rate_limit' => (int) $this->option('rate-limit'),
            'is_active' => true,
            'expires_at' => $this->option('expires') ? \Carbon\Carbon::parse($this->option('expires')) : null,
            'description' => $this->option('description'),
        ]);

        $this->newLine();
        $this->info('✅ API key generated successfully!');
        $this->newLine();

        $this->table(
            ['Field', 'Value'],
            [
                ['Client ID', $client->id],
                ['Name', $client->name],
                ['Email', $client->email],
                ['Rate Limit', $client->rate_limit . ' req/min'],
                ['Expires', $client->expires_at ?? 'Never'],
            ]
        );

        $this->newLine();
        $this->warn('⚠️  COPY THE API KEY BELOW — it will NOT be shown again:');
        $this->newLine();
        $this->line("   {$plainKey}");
        $this->newLine();

        return Command::SUCCESS;
    }
}
