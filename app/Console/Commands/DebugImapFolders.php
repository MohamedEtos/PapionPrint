<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Webklex\IMAP\Facades\Client;

class DebugImapFolders extends Command
{
    protected $signature = 'debug:imap-folders';
    protected $description = 'List all IMAP folders';

    public function handle()
    {
        $password = config('imap.accounts.default.password');
        if ($password === 'YOUR_PASSWORD_HERE' || empty($password)) {
            $this->error('ERROR: Password is set to placeholder or empty in .env!');
            $this->error('Please update IMAP_PASSWORD in your .env file with the actual password.');
            return;
        }

        try {
            $client = Client::account('default');
            // Disable cert validation for debugging
            $config = config('imap.accounts.default');
            $config['validate_cert'] = false;
            // Re-apply config to a new account instance if needed or we can just try connecting.
            // Client::account returns a Client instance. We might need to manually construct it to override config 
            // easily, or just update config before calling account.
            
            // Let's try updating config at runtime
            config(['imap.accounts.default.validate_cert' => false]);
            $client = Client::account('default');
            
            $client->connect();
            
            $folders = $client->getFolders();
            foreach($folders as $folder) {
                $this->info($folder->name . ' (' . $folder->path . ')');
            }
        } catch (\Exception $e) {
            $this->error('Connection failed: ' . $e->getMessage());
            $this->error('Host: ' . config('imap.accounts.default.host'));
            $this->error('Port: ' . config('imap.accounts.default.port'));
            $this->error('Username: ' . config('imap.accounts.default.username'));
            $this->error('Validate Cert: ' . (config('imap.accounts.default.validate_cert') ? 'true' : 'false'));
        }
    }
}
