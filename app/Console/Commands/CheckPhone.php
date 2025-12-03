<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Admin;
use App\Models\Lead;
use App\Models\ClientPhone;

class CheckPhone extends Command
{
    protected $signature = 'check:phone {number}';
    protected $description = 'Check if a phone number exists in the database';

    public function handle()
    {
        $phone = $this->argument('number');
        
        $this->info("Searching for phone number: {$phone}");
        $this->line("=====================================");
        
        // Check admins table
        $this->line("\n1. Checking ADMINS table...");
        $admins = DB::table('admins')
            ->where(function($q) use ($phone) {
                $q->where('phone', 'LIKE', "%{$phone}%")
                  ->orWhere('att_phone', 'LIKE', "%{$phone}%")
                  ->orWhere('phone', 'LIKE', "%0{$phone}%")
                  ->orWhere('att_phone', 'LIKE', "%0{$phone}%");
            })
            ->select('id', 'first_name', 'last_name', 'phone', 'att_phone', 'country_code', 'client_id', 'role', 'email')
            ->get();
        
        if ($admins->count() > 0) {
            $this->info("   Found {$admins->count()} record(s) in admins table:");
            foreach ($admins as $admin) {
                $this->line("   - ID: {$admin->id}");
                $this->line("     Name: {$admin->first_name} {$admin->last_name}");
                $this->line("     Client ID: {$admin->client_id}");
                $this->line("     Email: {$admin->email}");
                $this->line("     Phone: {$admin->phone}");
                $this->line("     Alt Phone: {$admin->att_phone}");
                $this->line("     Country Code: {$admin->country_code}");
                $this->line("     Role: {$admin->role}");
                $this->line("");
            }
        } else {
            $this->warn("   No records found in admins table");
        }
        
        // Check client_phones table
        $this->line("\n2. Checking CLIENT_PHONES table...");
        $clientPhones = DB::table('client_phones')
            ->where(function($q) use ($phone) {
                $q->where('client_phone', 'LIKE', "%{$phone}%")
                  ->orWhere('client_phone', 'LIKE', "%0{$phone}%");
            })
            ->select('id', 'client_id', 'client_phone', 'client_country_code', 'contact_type')
            ->get();
        
        if ($clientPhones->count() > 0) {
            $this->info("   Found {$clientPhones->count()} record(s) in client_phones table:");
            foreach ($clientPhones as $cp) {
                $this->line("   - ID: {$cp->id}");
                $this->line("     Client ID: {$cp->client_id}");
                $this->line("     Phone: {$cp->client_phone}");
                $this->line("     Country Code: {$cp->client_country_code}");
                $this->line("     Contact Type: {$cp->contact_type}");
                
                // Get client details
                $client = DB::table('admins')->where('id', $cp->client_id)->first();
                if ($client) {
                    $this->line("     Client Name: {$client->first_name} {$client->last_name}");
                    $this->line("     Client Ref: {$client->client_id}");
                }
                $this->line("");
            }
        } else {
            $this->warn("   No records found in client_phones table");
        }
        
        // Check leads table
        $this->line("\n3. Checking LEADS table...");
        $leads = DB::table('leads')
            ->where(function($q) use ($phone) {
                $q->where('phone', 'LIKE', "%{$phone}%")
                  ->orWhere('att_phone', 'LIKE', "%{$phone}%")
                  ->orWhere('phone', 'LIKE', "%0{$phone}%")
                  ->orWhere('att_phone', 'LIKE', "%0{$phone}%");
            })
            ->select('id', 'first_name', 'last_name', 'phone', 'att_phone', 'email', 'converted')
            ->get();
        
        if ($leads->count() > 0) {
            $this->info("   Found {$leads->count()} record(s) in leads table:");
            foreach ($leads as $lead) {
                $this->line("   - ID: {$lead->id}");
                $this->line("     Name: {$lead->first_name} {$lead->last_name}");
                $this->line("     Email: {$lead->email}");
                $this->line("     Phone: {$lead->phone}");
                $this->line("     Alt Phone: {$lead->att_phone}");
                $this->line("     Converted: " . ($lead->converted ? 'Yes' : 'No'));
                $this->line("");
            }
        } else {
            $this->warn("   No records found in leads table");
        }
        
        // Summary
        $total = $admins->count() + $clientPhones->count() + $leads->count();
        $this->line("\n=====================================");
        if ($total > 0) {
            $this->info("TOTAL FOUND: {$total} record(s)");
        } else {
            $this->error("NO RECORDS FOUND with phone number: {$phone}");
            $this->line("\nTrying with leading 0...");
            return $this->handle0Phone($phone);
        }
    }
    
    private function handle0Phone($phone)
    {
        $phoneWith0 = '0' . $phone;
        $this->info("Searching for: {$phoneWith0}");
        
        $count = 0;
        $count += DB::table('admins')->where('phone', 'LIKE', "%{$phoneWith0}%")->orWhere('att_phone', 'LIKE', "%{$phoneWith0}%")->count();
        $count += DB::table('client_phones')->where('client_phone', 'LIKE', "%{$phoneWith0}%")->count();
        $count += DB::table('leads')->where('phone', 'LIKE', "%{$phoneWith0}%")->orWhere('att_phone', 'LIKE', "%{$phoneWith0}%")->count();
        
        if ($count > 0) {
            $this->info("Found {$count} records with 0{$phone}");
        } else {
            $this->error("Still no records found");
        }
    }
}

