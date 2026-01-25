<?php

namespace App\Services;

use App\Models\Admin;
// use App\Models\ClientAddress; // Removed: client_addresses table doesn't exist in bansalcrm2 - addresses are stored in admins table
use App\Models\ClientPhone; // Note: bansalcrm2 uses ClientPhone instead of ClientContact
use App\Models\ClientEmail;
use App\Models\ClientPassportInformation;
use App\Models\ClientTravelInformation;
use App\Models\ClientCharacter;
use App\Models\ClientVisaCountry;
use App\Models\ActivitiesLog;
use App\Models\TestScore; // bansalcrm2 has TestScore table
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
            'marital_status' => $client->marital_status ?? $client->martial_status ?? null, // Note: bansalcrm2 might use martial_status
            
            // Address
            'address' => $client->address,
            'city' => $client->city,
            'state' => $client->state,
            'country' => $client->country,
            'zip' => $client->zip,
            
            // Passport
            'country_passport' => $client->country_passport ?? null,
            'passport_number' => $client->passport_number ?? null, // bansalcrm2 has passport_number in admins table
            
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
            
            // Other
            'naati_py' => $client->naati_py ?? null,
            'naati_test' => $client->naati_test ?? null,
            'naati_date' => $client->naati_date,
            'nati_language' => $client->nati_language ?? null,
            'py_test' => $client->py_test ?? null,
            'py_date' => $client->py_date,
            'py_field' => $client->py_field ?? null,
            'total_points' => $client->total_points ?? null,
            'start_process' => $client->start_process ?? null,
            'source' => $client->source,
            'type' => $client->type,
            'status' => $client->status,
            'profile_img' => $client->profile_img,
            'agent_id' => $client->agent_id ?? null,
            
            // Verification metadata (dates only, not staff IDs)
            'dob_verified_date' => $client->dob_verified_date ?? null,
            'dob_verify_document' => $client->dob_verify_document ?? null,
            'phone_verified_date' => $client->phone_verified_date ?? null,
            'visa_expiry_verified_at' => $client->visa_expiry_verified_at ?? null,
            
            // Emergency Contact (if exists)
            'emergency_country_code' => $client->emergency_country_code ?? null,
            'emergency_contact_no' => $client->emergency_contact_no ?? null,
            'emergency_contact_type' => $client->emergency_contact_type ?? null,
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
     */
    private function getClientContacts($clientId)
    {
        // Check if ClientPhone model exists, otherwise use ClientContact
        if (class_exists(\App\Models\ClientPhone::class)) {
            return \App\Models\ClientPhone::where('client_id', $clientId)
                ->get()
                ->map(function ($contact) {
                    return [
                        'contact_type' => $contact->contact_type ?? $contact->phone_type ?? null,
                        'country_code' => $contact->client_country_code ?? $contact->country_code ?? null,
                        'phone' => $contact->client_phone ?? $contact->phone ?? null,
                        'is_verified' => $contact->is_verified ?? false,
                        'verified_at' => $contact->verified_at ?? null,
                    ];
                })
                ->toArray();
        }
        
        // Fallback to ClientContact if ClientPhone doesn't exist
        return \App\Models\ClientContact::where('client_id', $clientId)
            ->get()
            ->map(function ($contact) {
                return [
                    'contact_type' => $contact->contact_type ?? null,
                    'country_code' => $contact->country_code ?? null,
                    'phone' => $contact->phone ?? null,
                    'is_verified' => $contact->is_verified ?? false,
                    'verified_at' => $contact->verified_at ?? null,
                ];
            })
            ->toArray();
    }

    /**
     * Get client emails
     */
    private function getClientEmails($clientId)
    {
        return ClientEmail::where('client_id', $clientId)
            ->get()
            ->map(function ($email) {
                return [
                    'email_type' => $email->email_type ?? null,
                    'email' => $email->email,
                    'is_verified' => $email->is_verified ?? false,
                    'verified_at' => $email->verified_at ?? null,
                ];
            })
            ->toArray();
    }

    /**
     * Get client passport information
     */
    private function getClientPassport($clientId)
    {
        $passport = ClientPassportInformation::where('client_id', $clientId)->first();
        
        if (!$passport) {
            return null;
        }

        return [
            'passport_number' => $passport->passport ?? $passport->passport_number ?? null,
            'passport_country' => $passport->passport_country ?? null,
            'passport_issue_date' => $passport->passport_issue_date ?? null,
            'passport_expiry_date' => $passport->passport_expiry_date ?? null,
        ];
    }

    /**
     * Get client travel information
     */
    private function getClientTravel($clientId)
    {
        return ClientTravelInformation::where('client_id', $clientId)
            ->get()
            ->map(function ($travel) {
                return [
                    'travel_country_visited' => $travel->travel_country_visited ?? null,
                    'travel_arrival_date' => $travel->travel_arrival_date ?? null,
                    'travel_departure_date' => $travel->travel_departure_date ?? null,
                    'travel_purpose' => $travel->travel_purpose ?? null,
                ];
            })
            ->toArray();
    }

    /**
     * Get client visa countries
     */
    private function getClientVisaCountries($clientId)
    {
        return ClientVisaCountry::where('client_id', $clientId)
            ->get()
            ->map(function ($visa) {
                return [
                    'visa_country' => $visa->visa_country ?? null,
                    'visa_type' => $visa->visa_type ?? null,
                    'visa_description' => $visa->visa_description ?? null,
                    'visa_expiry_date' => $visa->visa_expiry_date ?? null,
                    'visa_grant_date' => $visa->visa_grant_date ?? null,
                ];
            })
            ->toArray();
    }

    /**
     * Get client character information
     */
    private function getClientCharacter($clientId)
    {
        return ClientCharacter::where('client_id', $clientId)
            ->get()
            ->map(function ($character) {
                return [
                    'type_of_character' => $character->type_of_character ?? null,
                    'character_detail' => $character->character_detail ?? null,
                    'character_date' => $character->character_date ?? null,
                ];
            })
            ->toArray();
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
     * Get client activities (if structure matches)
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
                    'activity_type' => $activity->activity_type ?? null,
                    'followup_date' => $activity->followup_date ?? null,
                    'task_group' => $activity->task_group ?? null,
                    'task_status' => $activity->task_status ?? 0,
                    'created_at' => $activity->created_at ?? null,
                ];
            })
            ->toArray();
    }
}
