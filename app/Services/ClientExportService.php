<?php

namespace App\Services;

use App\Models\Admin;
// use App\Models\ClientAddress; // Removed: client_addresses table doesn't exist in bansalcrm2 - addresses are stored in admins table
use App\Models\ClientPhone; // Note: bansalcrm2 uses ClientPhone instead of ClientContact
use App\Models\ActivitiesLog;
use App\Models\TestScore; // bansalcrm2 has TestScore table
use App\Models\clientServiceTaken; // bansalcrm2 has client_service_takens table
use App\Models\VerifiedNumber; // bansalcrm2 has VerifiedNumber table for phone verification
use Illuminate\Support\Facades\Log;

class ClientExportService
{
    /**
     * Export client data to JSON format
     * 
     * @param int $clientId
     * @return array
     */
    public function exportClient($clientId)
    {
        try {
            $client = Admin::where('id', $clientId)
                ->where('role', 7) // Only clients
                ->first();

            if (!$client) {
                throw new \Exception('Client not found');
            }

            $exportData = [
                'version' => '1.0',
                'exported_at' => now()->toIso8601String(),
                'exported_from' => 'bansalcrm2',
                'client' => $this->getClientBasicData($client),
                'addresses' => $this->getClientAddresses($clientId),
                'contacts' => $this->getClientContacts($clientId),
                'emails' => $this->getClientEmails($clientId),
                'passport' => $this->getClientPassport($clientId),
                'travel' => $this->getClientTravel($clientId),
                'visa_countries' => $this->getClientVisaCountries($clientId),
                'character' => $this->getClientCharacter($clientId),
                'test_scores' => $this->getClientTestScores($clientId), // bansalcrm2 has TestScore table
                'services' => $this->getClientServices($clientId), // bansalcrm2 has client_service_takens table
                'activities' => $this->getClientActivities($clientId),
            ];

            return $exportData;
        } catch (\Exception $e) {
            Log::error('Client export error: ' . $e->getMessage(), [
                'client_id' => $clientId,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Get basic client data (fields that exist in both systems)
     */
    private function getClientBasicData($client)
    {
        return [
            // Basic Identity
            'client_id' => $client->client_id ?? null,
            'first_name' => $client->first_name,
            'last_name' => $client->last_name,
            'email' => $client->email,
            'phone' => $client->phone,
            'country_code' => $client->country_code,
            'telephone' => $client->telephone ?? null,
            
            // Personal Information
            'dob' => $client->dob,
            'age' => $client->age,
            'gender' => $client->gender,
            'marital_status' => $client->marital_status ?? $client->martial_status ?? null, // Note: bansalcrm2 uses martial_status
            
            // Address
            'address' => $client->address,
            'city' => $client->city,
            'state' => $client->state,
            'country' => $client->country,
            'zip' => $client->zip,
            
            // Passport
            'country_passport' => $client->country_passport ?? null,
            'passport_number' => $client->passport_number ?? null, // bansalcrm2 has passport_number in admins table
            
            // Visa Information
            'visa_type' => $client->visa_type ?? null,
            'visa_opt' => $client->visa_opt ?? null,
            'visaExpiry' => $client->visaExpiry ?? null, // Uses accessor, column is visaexpiry
            'preferredIntake' => $client->preferredIntake ?? null, // Uses accessor, column is preferredintake
            
            // Professional Details (bansalcrm2 specific)
            'nomi_occupation' => $client->nomi_occupation ?? null,
            'skill_assessment' => $client->skill_assessment ?? null,
            'high_quali_aus' => $client->high_quali_aus ?? null,
            'high_quali_overseas' => $client->high_quali_overseas ?? null,
            'relevant_work_exp_aus' => $client->relevant_work_exp_aus ?? null,
            'relevant_work_exp_over' => $client->relevant_work_exp_over ?? null,
            
            // Additional Contact
            'att_email' => $client->att_email ?? null,
            'att_phone' => $client->att_phone ?? null,
            'att_country_code' => $client->att_country_code ?? null,
            'email_type' => $client->email_type ?? null,
            
            // Internal Information
            'service' => $client->service ?? null,
            'assignee' => $client->assignee ?? null,
            'lead_quality' => $client->lead_quality ?? null,
            'comments_note' => $client->comments_note ?? null,
            'married_partner' => $client->married_partner ?? null,
            'followers' => $client->followers ?? null,
            'tagname' => $client->tagname ?? null,
            'related_files' => $client->related_files ?? null,
            'applications' => $client->applications ?? null,
            
            // System Fields
            'office_id' => $client->office_id ?? null,
            // Note: 'verified' field may not exist in bansalcrm2 admins table
            // Verification is handled separately for email (manual_email_phone_verified) and phone (VerifiedNumber table)
            'verified' => isset($client->attributes['verified']) ? $client->attributes['verified'] : 0,
            'show_dashboard_per' => isset($client->attributes['show_dashboard_per']) ? $client->attributes['show_dashboard_per'] : 0,
            // Note: Archive status (is_archived, archived_by) is NOT exported - archive status should not transfer between systems
            
            // Other
            'naati_py' => $client->naati_py ?? null,
            'total_points' => $client->total_points ?? null,
            'start_process' => $client->start_process ?? null,
            'source' => $client->source,
            'type' => $client->type,
            'status' => $client->status,
            'profile_img' => $client->profile_img,
            'agent_id' => $client->agent_id ?? null,
        ];
    }

    /**
     * Get client addresses
     * Note: bansalcrm2 doesn't have a separate client_addresses table
     * Addresses are stored directly in the admins table
     * Returns empty array since primary address is already in the client object
     */
    private function getClientAddresses($clientId)
    {
        // bansalcrm2 stores addresses in the admins table (address, city, state, country, zip)
        // These fields are already exported in getClientBasicData()
        // Return empty array to maintain JSON structure compatibility
        return [];
    }

    /**
     * Get client contacts (phone numbers)
     * Note: bansalcrm2 uses ClientPhone model
     * Phone verification is stored in VerifiedNumber table
     */
    private function getClientContacts($clientId)
    {
        if (!class_exists(\App\Models\ClientPhone::class)) {
            return [];
        }
        
        return \App\Models\ClientPhone::where('client_id', $clientId)
            ->get()
            ->map(function ($contact) {
                // Build full phone number for verification lookup
                $fullPhoneNumber = ($contact->client_country_code ?? '') . ($contact->client_phone ?? '');
                
                // Check if phone is verified in VerifiedNumber table
                $verifiedNumber = null;
                if (!empty($fullPhoneNumber) && class_exists(\App\Models\VerifiedNumber::class)) {
                    $verifiedNumber = \App\Models\VerifiedNumber::where('phone_number', $fullPhoneNumber)
                        ->where('is_verified', true)
                        ->first();
                }
                
                return [
                    'contact_type' => $contact->contact_type ?? null,
                    'country_code' => $contact->client_country_code ?? null, // Note: column is client_country_code
                    'phone' => $contact->client_phone ?? null, // Note: column is client_phone
                    'is_verified' => $verifiedNumber ? true : false,
                    'verified_at' => $verifiedNumber ? $verifiedNumber->verified_at : null,
                ];
            })
            ->toArray();
    }

    /**
     * Get client emails
     * Note: bansalcrm2 stores emails in admins table, not in separate client_emails table
     */
    private function getClientEmails($clientId)
    {
        $client = Admin::find($clientId);
        if (!$client) {
            return [];
        }
        
        $emails = [];
        
        // Main email from admins table
        if (!empty($client->email)) {
            $emails[] = [
                'email' => $client->email,
                'email_type' => $client->email_type ?? 'Personal',
                'is_verified' => ($client->manual_email_phone_verified ?? 0) == 1,
                'verified_at' => $client->email_verified_at ?? null, // email_verified_at exists in admins table
            ];
        }
        
        // Additional email from admins table
        if (!empty($client->att_email)) {
            $emails[] = [
                'email' => $client->att_email,
                'email_type' => 'Additional',
                'is_verified' => false, // att_email doesn't have separate verification
                'verified_at' => null,
            ];
        }
        
        return $emails;
    }

    /**
     * Get client passport information
     * Note: bansalcrm2 stores passport data in admins table, not in separate client_passport_informations table
     */
    private function getClientPassport($clientId)
    {
        $client = Admin::find($clientId);
        if (!$client) {
            return null;
        }
        
        // If no passport data, return null
        if (empty($client->country_passport) && empty($client->passport_number)) {
            return null;
        }
        
        return [
            'passport_number' => $client->passport_number ?? null,
            'passport_country' => $client->country_passport ?? null,
            'passport_issue_date' => null, // Not stored in bansalcrm2
            'passport_expiry_date' => null, // Not stored in bansalcrm2
        ];
    }

    /**
     * Get client travel information
     * Note: bansalcrm2 doesn't have client_travel_informations table
     */
    private function getClientTravel($clientId)
    {
        // bansalcrm2 doesn't have client_travel_informations table
        // Return empty array to maintain JSON structure compatibility
        return [];
    }

    /**
     * Get client visa countries
     * Note: bansalcrm2 stores visa data in admins table, not in separate client_visa_countries table
     */
    private function getClientVisaCountries($clientId)
    {
        $client = Admin::find($clientId);
        if (!$client) {
            return [];
        }
        
        // If no visa data, return empty array
        if (empty($client->visa_type) && empty($client->visa_opt) && empty($client->visaExpiry)) {
            return [];
        }
        
        // Convert admins table visa fields to visa_countries array format
        return [
            [
                'visa_country' => $client->country ?? null, // Use client's country as visa country
                'visa_type' => $client->visa_type ?? null,
                'visa_description' => $client->visa_opt ?? null,
                'visa_expiry_date' => $client->visaExpiry ?? null, // Uses accessor for visaexpiry column
                'visa_grant_date' => null, // Not stored in bansalcrm2
            ]
        ];
    }

    /**
     * Get client character information
     * Note: bansalcrm2 doesn't have client_characters table
     */
    private function getClientCharacter($clientId)
    {
        // bansalcrm2 doesn't have client_characters table
        // Return empty array to maintain JSON structure compatibility
        return [];
    }

    /**
     * Get client test scores (bansalcrm2 specific)
     */
    private function getClientTestScores($clientId)
    {
        if (!class_exists(\App\Models\TestScore::class)) {
            return [];
        }

        return \App\Models\TestScore::where('client_id', $clientId)
            ->where('type', 'client')
            ->get()
            ->map(function ($test) {
                return [
                    'type' => $test->type ?? null,
                    'toefl_Listening' => $test->toefl_Listening ?? null,
                    'toefl_Reading' => $test->toefl_Reading ?? null,
                    'toefl_Writing' => $test->toefl_Writing ?? null,
                    'toefl_Speaking' => $test->toefl_Speaking ?? null,
                    'toefl_Date' => $test->toefl_Date ?? null,
                    'ilets_Listening' => $test->ilets_Listening ?? null,
                    'ilets_Reading' => $test->ilets_Reading ?? null,
                    'ilets_Writing' => $test->ilets_Writing ?? null,
                    'ilets_Speaking' => $test->ilets_Speaking ?? null,
                    'ilets_Date' => $test->ilets_Date ?? null,
                    'pte_Listening' => $test->pte_Listening ?? null,
                    'pte_Reading' => $test->pte_Reading ?? null,
                    'pte_Writing' => $test->pte_Writing ?? null,
                    'pte_Speaking' => $test->pte_Speaking ?? null,
                    'pte_Date' => $test->pte_Date ?? null,
                    'score_1' => $test->score_1 ?? null,
                    'score_2' => $test->score_2 ?? null,
                    'score_3' => $test->score_3 ?? null,
                ];
            })
            ->toArray();
    }

    /**
     * Get client services taken (bansalcrm2 specific)
     */
    private function getClientServices($clientId)
    {
        if (!class_exists(\App\Models\clientServiceTaken::class)) {
            return [];
        }
        
        return \App\Models\clientServiceTaken::where('client_id', $clientId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($service) {
                $serviceData = [
                    'service_type' => $service->service_type ?? null,
                    'created_at' => $service->created_at ?? null,
                    'updated_at' => $service->updated_at ?? null,
                ];
                
                // Add migration-specific fields
                if ($service->service_type === 'Migration') {
                    $serviceData['mig_ref_no'] = $service->mig_ref_no ?? null;
                    $serviceData['mig_service'] = $service->mig_service ?? null;
                    $serviceData['mig_notes'] = $service->mig_notes ?? null;
                }
                
                // Add education-specific fields
                if ($service->service_type === 'Education') {
                    $serviceData['edu_course'] = $service->edu_course ?? null;
                    $serviceData['edu_college'] = $service->edu_college ?? null;
                    $serviceData['edu_service_start_date'] = $service->edu_service_start_date ?? null;
                    $serviceData['edu_notes'] = $service->edu_notes ?? null;
                }
                
                return $serviceData;
            })
            ->toArray();
    }

    /**
     * Get client activities
     * Note: bansalcrm2 activities_logs table doesn't have activity_type, followup_date, task_group columns
     */
    private function getClientActivities($clientId)
    {
        return ActivitiesLog::where('client_id', $clientId)
            ->orderBy('created_at', 'desc')
            ->limit(100) // Limit to recent 100 activities
            ->get()
            ->map(function ($activity) {
                return [
                    'subject' => $activity->subject ?? null,
                    'description' => $activity->description ?? null,
                    'use_for' => $activity->use_for ?? null,
                    'task_status' => $activity->task_status ?? 0,
                    'pin' => $activity->pin ?? 0,
                    'created_at' => $activity->created_at ?? null,
                    'created_by' => $activity->created_by ?? null,
                ];
            })
            ->toArray();
    }
}
