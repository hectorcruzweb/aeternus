<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Token;

class RevokePassportTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Example usage: php artisan passport:revoke --client=1
     */
    protected $signature = 'passport:revoke {--client= : Client ID to revoke tokens for}';

    /**
     * The console command description.
     */
    protected $description = 'Revoke all Passport access and refresh tokens for a given client ID.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $clientId = $this->option('client');

        if (!$clientId) {
            $this->error('❌ Please provide a client ID using --client=');
            return 1;
        }

        $this->info("Revoking all tokens for Passport client ID: {$clientId}...");

        DB::transaction(function () use ($clientId) {
            // Revoke all access tokens for the given client
            Token::where('client_id', $clientId)->update(['revoked' => true]);

            // Revoke all refresh tokens linked to those access tokens
            DB::table('oauth_refresh_tokens')
                ->whereIn('access_token_id', function ($query) use ($clientId) {
                    $query->select('id')
                        ->from('oauth_access_tokens')
                        ->where('client_id', $clientId);
                })
                ->update(['revoked' => true]);
        });

        $this->info('✅ All tokens successfully revoked!');
        return 0;
    }
}
