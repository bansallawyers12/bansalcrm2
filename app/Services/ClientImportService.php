<?php

namespace App\Services;

use App\Models\Admin;
// use App\Models\ClientAddress; // Removed: client_addresses table doesn't exist in bansalcrm2 - addresses are stored in admins table
use App\Models\ClientPhone; // bansalcrm2 uses ClientPhone
use App\Models\ClientEmail;
use App\Models\ClientPassportInformation;
use App\Models\ClientTravelInformation;
use App\Models\ClientCharacter;
use App\Models\ClientVisaCountry;
use App\Models\ActivitiesLog;
use App\Models\TestScore; // bansalcrm2 has TestScore table
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ClientImportService
{
    /**
     * Import client data from JSON
     * 
     * @param array $importData
     * @param bool $skipDuplicates
     * @return array ['success' => bool, 'client_id' => int|null, 'message' => string]
     */
    public function importClient(array $importData, $skipDuplicates = true)
    {
        DB::beginTransaction();

        try {
            // Validate import data structure
            if (!isset($importData['client'])) {
                throw new \Exception('Invalid import file: missing client data');
            }

            $clientData = $importData['client'];

            // Check for duplicate email if skip_duplicates is enabled
            if ($skipDuplicates && !empty($clientData['email'])) {
                $existingClient = Admin::where('email', $clientData['email'])
                    ->where('role', 7)
                    ->first();

                if ($existingClient) {
                    DB::rollBack();
                    return [
                        'success' => false,
                        'client_id' => null,
                        'message' => 'Client with email ' . $clientData['email'] . ' already exists. Import skipped.'
                    ];
                }
            }

            // Create the client first (we need the ID to generate client_id)
            $client = new Admin();
            $client->first_name = $clientData['first_name'];
            $client->last_name = $clientData['last_name'] ?? null;
            $client->email = $clientData['email'];
            $client->phone = $clientData['phone'] ?? null;
            $client->country_code = $clientData['country_code'] ?? null;
            $client->telephone = $clientData['telephone'] ?? null;
            
            // Personal Information
            $client->dob = $this->parseDate($clientData['dob'] ?? null);
            $client->age = $clientData['age'] ?? null;
            $client->gender = $clientData['gender'] ?? null;
            $client->martial_status = $clientData['marital_status'] ?? null; // Note: bansalcrm2 uses martial_status
            
            // Address
            $client->address = $clientData['address'] ?? null;
            $client->city = $clientData['city'] ?? null;
            $client->state = $this->mapState($clientData['state'] ?? null);
            $client->country = $this->mapCountry($clientData['country'] ?? null);
            $client->zip = $clientData['zip'] ?? null;
            
            // Passport
            $client->country_passport = $clientData['country_passport'] ?? null;
            $client->passport_number = $clientData['passport_number'] ?? null; // bansalcrm2 has this in admins table
            
            // Professional Details (bansalcrm2 specific)
            $client->nomi_occupation = $clientData['nomi_occupation'] ?? null;
            $client->skill_assessment = $clientData['skill_assessment'] ?? null;
            $client->high_quali_aus = $clientData['high_quali_aus'] ?? null;
            $client->high_quali_overseas = $clientData['high_quali_overseas'] ?? null;
            $client->relevant_work_exp_aus = $clientData['relevant_work_exp_aus'] ?? null;
            $client->relevant_work_exp_over = $clientData['relevant_work_exp_over'] ?? null;
            
            // Additional Contact
            $client->att_email = $clientData['att_email'] ?? null;
            $client->att_phone = $clientData['att_phone'] ?? null;
            $client->att_country_code = $clientData['att_country_code'] ?? null;
            
            // Other
            $client->naati_py = $clientData['naati_py'] ?? null;
            // naati_test, naati_date, nati_language, py_test, py_date, py_field removed - columns don't exist in bansalcrm2 database
            $client->total_points = $clientData['total_points'] ?? null;
            $client->start_process = $clientData['start_process'] ?? null;
            $client->source = $clientData['source'] ?? null;
            
            // Email and contact types (these exist in database)
            $client->email_type = $clientData['email_type'] ?? null;
            $client->contact_type = $clientData['contact_type'] ?? null;
            $client->type = $clientData['type'] ?? 'client';
            // Ensure status is an integer (handle string "1" from JSON)
            $client->status = is_numeric($clientData['status'] ?? 1) ? (int)$clientData['status'] : 1;
            $client->profile_img = $clientData['profile_img'] ?? null;
            $client->agent_id = $clientData['agent_id'] ?? null;
            
            // Verification metadata fields removed - columns don't exist in bansalcrm2 database:
            // dob_verified_date, dob_verify_document, phone_verified_date, visa_expiry_verified_at
            
            // Emergency Contact fields removed - columns don't exist in bansalcrm2 database:
            // emergency_country_code, emergency_contact_no, emergency_contact_type
            
            // System fields
            $client->role = 7; // Client role
            $client->password = Hash::make('CLIENT_IMPORT_' . time()); // Temporary password
            $client->decrypt_password = null;
            // Status already set above, don't override
            
            // Required fields for new clients (set defaults if not provided)
            $client->is_archived = 0; // Not archived by default
            $client->verified = 0; // New clients are not verified yet
            $client->show_dashboard_per = 0; // Clients don't have dashboard access
            $client->office_id = Auth::user()->office_id ?? null; // Use current user's office_id if available
            
            $client->save();
            $newClientId = $client->id;

            // Generate client_id after saving (format: FIRSTNAME + YYMM + ID)
            $first_name = $clientData['first_name'] ?? 'CLIENT';
            $client->client_id = strtoupper($first_name) . date('ym') . $newClientId;
            $client->save();

            // Skip addresses import - bansalcrm2 doesn't have a separate client_addresses table
            // Addresses are stored directly in the admins table (address, city, state, country, zip)
            // The primary address is already imported from the client object above (lines 74-78)
            // if (isset($importData['addresses']) && is_array($importData['addresses'])) {
            //     // Multiple addresses not supported in bansalcrm2 - only primary address in admins table
            // }

            // Import contacts (phone numbers) - Use ClientPhone for bansalcrm2
            if (isset($importData['contacts']) && is_array($importData['contacts'])) {
                foreach ($importData['contacts'] as $contactData) {
                    if (class_exists(\App\Models\ClientPhone::class)) {
                        \App\Models\ClientPhone::create([
                            'client_id' => $newClientId,
                            'admin_id' => Auth::id(),
                            'contact_type' => $contactData['contact_type'] ?? null,
                            'client_country_code' => $contactData['country_code'] ?? null,
                            'client_phone' => $contactData['phone'] ?? null,
                            'is_verified' => $contactData['is_verified'] ?? false,
                            'verified_at' => $this->parseDateTime($contactData['verified_at'] ?? null),
                        ]);
                    }
                }
            }

            // Import emails
            if (isset($importData['emails']) && is_array($importData['emails'])) {
                foreach ($importData['emails'] as $emailData) {
                    ClientEmail::create([
                        'client_id' => $newClientId,
                        'admin_id' => Auth::id(),
                        'email_type' => $emailData['email_type'] ?? null,
                        'email' => $emailData['email'] ?? null,
                        'is_verified' => $emailData['is_verified'] ?? false,
                        'verified_at' => $this->parseDateTime($emailData['verified_at'] ?? null),
                    ]);
                }
            }

            // Import passport
            if (!empty($importData['passport']) && is_array($importData['passport'])) {
                ClientPassportInformation::create([
                    'client_id' => $newClientId,
                    'admin_id' => Auth::id(),
                    'passport' => $importData['passport']['passport_number'] ?? $importData['passport']['passport'] ?? null,
                    'passport_country' => $importData['passport']['passport_country'] ?? null,
                    'passport_issue_date' => $this->parseDate($importData['passport']['passport_issue_date'] ?? null),
                    'passport_expiry_date' => $this->parseDate($importData['passport']['passport_expiry_date'] ?? null),
                ]);
            }

            // Import travel information
            if (isset($importData['travel']) && is_array($importData['travel'])) {
                foreach ($importData['travel'] as $travelData) {
                    ClientTravelInformation::create([
                        'client_id' => $newClientId,
                        'admin_id' => Auth::id(),
                        'travel_country_visited' => $travelData['travel_country_visited'] ?? null,
                        'travel_arrival_date' => $this->parseDate($travelData['travel_arrival_date'] ?? null),
                        'travel_departure_date' => $this->parseDate($travelData['travel_departure_date'] ?? null),
                        'travel_purpose' => $travelData['travel_purpose'] ?? null,
                    ]);
                }
            }

            // Import visa countries
            if (isset($importData['visa_countries']) && is_array($importData['visa_countries'])) {
                foreach ($importData['visa_countries'] as $visaData) {
                    ClientVisaCountry::create([
                        'client_id' => $newClientId,
                        'admin_id' => Auth::id(),
                        'visa_country' => $visaData['visa_country'] ?? null,
                        'visa_type' => $visaData['visa_type'] ?? null,
                        'visa_description' => $visaData['visa_description'] ?? null,
                        'visa_expiry_date' => $this->parseDate($visaData['visa_expiry_date'] ?? null),
                        'visa_grant_date' => $this->parseDate($visaData['visa_grant_date'] ?? null),
                    ]);
                }
            }

            // Import character information
            if (isset($importData['character']) && is_array($importData['character'])) {
                foreach ($importData['character'] as $characterData) {
                    ClientCharacter::create([
                        'client_id' => $newClientId,
                        'admin_id' => Auth::id(),
                        'type_of_character' => $characterData['type_of_character'] ?? null,
                        'character_detail' => $characterData['character_detail'] ?? null,
                        'character_date' => $this->parseDate($characterData['character_date'] ?? null),
                    ]);
                }
            }

            // Import test scores (bansalcrm2 specific)
            if (isset($importData['test_scores']) && is_array($importData['test_scores']) && class_exists(\App\Models\TestScore::class)) {
                foreach ($importData['test_scores'] as $testData) {
                    \App\Models\TestScore::create([
                        'client_id' => $newClientId,
                        'user_id' => null,
                        'type' => 'client',
                        'toefl_Listening' => $testData['toefl_Listening'] ?? null,
                        'toefl_Reading' => $testData['toefl_Reading'] ?? null,
                        'toefl_Writing' => $testData['toefl_Writing'] ?? null,
                        'toefl_Speaking' => $testData['toefl_Speaking'] ?? null,
                        'toefl_Date' => $this->parseDate($testData['toefl_Date'] ?? null),
                        'ilets_Listening' => $testData['ilets_Listening'] ?? null,
                        'ilets_Reading' => $testData['ilets_Reading'] ?? null,
                        'ilets_Writing' => $testData['ilets_Writing'] ?? null,
                        'ilets_Speaking' => $testData['ilets_Speaking'] ?? null,
                        'ilets_Date' => $this->parseDate($testData['ilets_Date'] ?? null),
                        'pte_Listening' => $testData['pte_Listening'] ?? null,
                        'pte_Reading' => $testData['pte_Reading'] ?? null,
                        'pte_Writing' => $testData['pte_Writing'] ?? null,
                        'pte_Speaking' => $testData['pte_Speaking'] ?? null,
                        'pte_Date' => $this->parseDate($testData['pte_Date'] ?? null),
                        'score_1' => $testData['score_1'] ?? null,
                        'score_2' => $testData['score_2'] ?? null,
                        'score_3' => $testData['score_3'] ?? null,
                    ]);
                }
            }

            // Import activities (if structure matches)
            if (isset($importData['activities']) && is_array($importData['activities'])) {
                foreach ($importData['activities'] as $activityData) {
                    $activityCreatedAt = $this->parseDateTime($activityData['created_at'] ?? null);
                    ActivitiesLog::create([
                        'client_id' => $newClientId,
                        'created_by' => Auth::id(),
                        'subject' => $activityData['subject'] ?? 'Imported Activity',
                        'description' => $activityData['description'] ?? null,
                        'activity_type' => $activityData['activity_type'] ?? 'activity',
                        'followup_date' => $this->parseDateTime($activityData['followup_date'] ?? null),
                        'task_group' => $activityData['task_group'] ?? null,
                        'task_status' => $activityData['task_status'] ?? 0,
                        'created_at' => $activityCreatedAt,
                        'updated_at' => $activityCreatedAt ?? now(),
                    ]);
                }
            }

            DB::commit();

            return [
                'success' => true,
                'client_id' => $newClientId,
                'client_id_reference' => $client->client_id,
                'message' => 'Client imported successfully. Client ID: ' . $client->client_id
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log full error details for debugging
            $fullError = $e->getMessage();
            $errorDetails = [
                'error' => $fullError,
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ];
            
            // Extract SQL error details if available
            if (method_exists($e, 'getPrevious') && $e->getPrevious()) {
                $errorDetails['previous'] = $e->getPrevious()->getMessage();
                $fullError = $e->getPrevious()->getMessage() . ' | ' . $fullError;
            }
            
            Log::error('Client import error', $errorDetails);

            // Provide more user-friendly error messages
            $errorMessage = $fullError;
            
            // Check for common database errors and provide clearer messages
            if (strpos($errorMessage, 'SQLSTATE') !== false || strpos($errorMessage, 'violates') !== false) {
                // Extract column name from error if possible
                preg_match('/column "([^"]+)"/i', $errorMessage, $columnMatch);
                $columnName = !empty($columnMatch[1]) ? $columnMatch[1] : 'unknown';
                
                if (strpos($errorMessage, 'violates not-null constraint') !== false || strpos($errorMessage, 'null value in column') !== false) {
                    $errorMessage = "Database error: Required field '{$columnName}' is missing or null. Please check the import file contains all required client data.";
                } elseif (strpos($errorMessage, 'duplicate key value') !== false || strpos($errorMessage, 'unique constraint') !== false) {
                    $errorMessage = 'Database error: Duplicate record detected. The client may already exist in the system (email, phone, or client_id already exists).';
                } elseif (strpos($errorMessage, 'foreign key constraint') !== false) {
                    $errorMessage = 'Database error: Invalid reference data. Please check agent_id, country, or state values in the import file.';
                } elseif (strpos($errorMessage, 'invalid input syntax') !== false) {
                    $errorMessage = "Database error: Invalid data format for field '{$columnName}'. Please check the data type matches the expected format.";
                } else {
                    // Show the actual error for debugging
                    $errorMessage = 'Database error: ' . $fullError . ' Please check the import file format and try again.';
                }
            } elseif (strpos($errorMessage, 'Call to a member function') !== false) {
                $errorMessage = 'Data format error: Invalid data structure in import file. Please verify the JSON file is correctly formatted.';
            }

            return [
                'success' => false,
                'client_id' => null,
                'message' => $errorMessage
            ];
        }
    }

    /**
     * Parse date string to Y-m-d format
     */
    private function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }

        try {
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                return $date;
            }
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning('Failed to parse date: ' . $date, ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Parse datetime string
     */
    private function parseDateTime($datetime)
    {
        if (empty($datetime)) {
            return null;
        }

        try {
            return Carbon::parse($datetime);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Map state
     */
    private function mapState($state)
    {
        if (is_numeric($state)) {
            return $state;
        }
        return $state;
    }

    /**
     * Map country
     */
    private function mapCountry($country)
    {
        if (is_numeric($country)) {
            return $country;
        }

        if (is_string($country) && strlen($country) <= 3) {
            $countryModel = \App\Models\Country::where('sortname', $country)->first();
            if ($countryModel) {
                return $countryModel->id;
            }
        }

        return $country;
    }
}
