<?php

namespace App\Services;

use App\Models\Admin;
// use App\Models\ClientAddress; // Removed: client_addresses table doesn't exist in bansalcrm2 - addresses are stored in admins table
use App\Models\ClientPhone; // bansalcrm2 uses ClientPhone
// Removed: ClientEmail, ClientPassportInformation, ClientTravelInformation, ClientCharacter, ClientVisaCountry - tables don't exist
use App\Models\ActivitiesLog;
use App\Models\TestScore; // bansalcrm2 has TestScore table
use App\Models\clientServiceTaken; // bansalcrm2 has client_service_takens table
use App\Helpers\PhoneHelper;
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

            // Check for duplicate email and phone number if skip_duplicates is enabled
            if ($skipDuplicates) {
                $emailMatch = false;
                $phoneMatch = false;
                $matchedClient = null;
                $matchedPhoneNumbers = [];

                // Check for duplicate email
                if (!empty($clientData['email'])) {
                    $existingClientByEmail = Admin::where('email', $clientData['email'])
                        ->where('role', 7)
                        ->first();

                    if ($existingClientByEmail) {
                        $emailMatch = true;
                        $matchedClient = $existingClientByEmail;
                    }
                }

                // Check for duplicate phone numbers
                $importPhoneNumbers = $this->extractPhoneNumbersFromImport($importData);
                
                if (!empty($importPhoneNumbers)) {
                    foreach ($importPhoneNumbers as $importPhone) {
                        $normalizedCountryCode = PhoneHelper::normalizeCountryCode($importPhone['country_code'] ?? '');
                        $normalizedPhone = $this->normalizePhoneNumber($importPhone['phone'] ?? '');
                        
                        if (!empty($normalizedPhone)) {
                            // Check ClientPhone table
                            $existingPhones = ClientPhone::where('client_country_code', $normalizedCountryCode)->get();
                            
                            foreach ($existingPhones as $existingPhone) {
                                // Normalize both sides for comparison
                                $existingCountryCode = PhoneHelper::normalizeCountryCode($existingPhone->getAttributes()['client_country_code'] ?? '');
                                $existingPhoneNumber = $this->normalizePhoneNumber($existingPhone->getAttributes()['client_phone'] ?? '');
                                
                                // Check if country code and phone number match (both normalized)
                                if ($normalizedCountryCode === $existingCountryCode && 
                                    $normalizedPhone === $existingPhoneNumber && 
                                    !empty($existingPhoneNumber)) {
                                    
                                    // Get the client that owns this phone number
                                    $existingClientByPhone = Admin::where('id', $existingPhone->client_id)
                                        ->where('role', 7)
                                        ->first();

                                    if ($existingClientByPhone) {
                                        $phoneMatch = true;
                                        if (!$matchedClient) {
                                            $matchedClient = $existingClientByPhone;
                                        }
                                        
                                        // Format for display (use original phone format from import)
                                        $displayPhone = PhoneHelper::formatPhoneNumber(
                                            $normalizedCountryCode,
                                            $importPhone['phone'] ?? $normalizedPhone
                                        );
                                        
                                        // Avoid duplicates in matched phone numbers array
                                        if (!in_array($displayPhone, $matchedPhoneNumbers)) {
                                            $matchedPhoneNumbers[] = $displayPhone;
                                        }
                                        
                                        // Break inner loop since we found a match
                                        break 2; // Break both loops
                                    }
                                }
                            }
                            
                            // Also check Admin table phone fields (phone and att_phone)
                            // Note: Admin table stores phone without country code separation in some cases
                            // We'll check if the normalized phone matches any client's phone or att_phone
                            // Using a more efficient approach: query clients and check phone fields
                            $clientsWithPhone = Admin::where('role', 7)
                                ->where(function($query) use ($normalizedPhone) {
                                    // Check if normalized phone matches phone field (after normalization)
                                    // We'll need to normalize in PHP since DB doesn't have a normalize function
                                    $query->whereNotNull('phone')
                                          ->orWhereNotNull('att_phone');
                                })
                                ->get();
                            
                            foreach ($clientsWithPhone as $client) {
                                // Check main phone field
                                if (!empty($client->phone)) {
                                    $clientPhoneNormalized = $this->normalizePhoneNumber($client->phone);
                                    if ($normalizedPhone === $clientPhoneNormalized && !empty($clientPhoneNormalized)) {
                                        $phoneMatch = true;
                                        if (!$matchedClient) {
                                            $matchedClient = $client;
                                        }
                                        
                                        $displayPhone = PhoneHelper::formatPhoneNumber(
                                            $normalizedCountryCode,
                                            $importPhone['phone'] ?? $normalizedPhone
                                        );
                                        
                                        if (!in_array($displayPhone, $matchedPhoneNumbers)) {
                                            $matchedPhoneNumbers[] = $displayPhone;
                                        }
                                        break 2; // Break both loops
                                    }
                                }
                                
                                // Check att_phone field
                                if (!empty($client->att_phone)) {
                                    $clientAttPhoneNormalized = $this->normalizePhoneNumber($client->att_phone);
                                    if ($normalizedPhone === $clientAttPhoneNormalized && !empty($clientAttPhoneNormalized)) {
                                        $phoneMatch = true;
                                        if (!$matchedClient) {
                                            $matchedClient = $client;
                                        }
                                        
                                        $displayPhone = PhoneHelper::formatPhoneNumber(
                                            $normalizedCountryCode,
                                            $importPhone['phone'] ?? $normalizedPhone
                                        );
                                        
                                        if (!in_array($displayPhone, $matchedPhoneNumbers)) {
                                            $matchedPhoneNumbers[] = $displayPhone;
                                        }
                                        break 2; // Break both loops
                                    }
                                }
                            }
                        }
                    }
                }

                // If either email or phone matches, skip import and return appropriate message
                if ($emailMatch || $phoneMatch) {
                    DB::rollBack();
                    
                    $messages = [];
                    if ($emailMatch) {
                        $messages[] = 'email ' . $clientData['email'];
                    }
                    if ($phoneMatch) {
                        if (count($matchedPhoneNumbers) === 1) {
                            $messages[] = 'phone number ' . $matchedPhoneNumbers[0];
                        } else {
                            $messages[] = 'phone number(s) ' . implode(', ', $matchedPhoneNumbers);
                        }
                    }
                    
                    $message = 'Client with ' . implode(' and ', $messages) . ' already exists. Import skipped.';
                    
                    return [
                        'success' => false,
                        'client_id' => null,
                        'message' => $message
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
            $client->marital_status = $clientData['marital_status'] ?? null;
            
            // Address
            $client->address = $clientData['address'] ?? null;
            $client->city = $clientData['city'] ?? null;
            $client->state = $this->mapState($clientData['state'] ?? null);
            $client->country = $this->mapCountry($clientData['country'] ?? null);
            $client->zip = $clientData['zip'] ?? null;
            
            // Passport
            $client->country_passport = $clientData['country_passport'] ?? null;
            $client->passport_number = $clientData['passport_number'] ?? null; // bansalcrm2 has this in admins table
            
            // Visa Information
            $client->visa_type = $clientData['visa_type'] ?? null;
            $client->visa_opt = $clientData['visa_opt'] ?? null;
            $client->visaexpiry = $this->parseDate($clientData['visaExpiry'] ?? null); // Column is visaexpiry, JSON uses visaExpiry
            $client->preferredintake = $this->parseDate($clientData['preferredIntake'] ?? null); // Column is preferredintake, JSON uses preferredIntake
            
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
            $client->email_type = $clientData['email_type'] ?? null;
            
            // Internal Information
            $client->service = $clientData['service'] ?? null;
            $client->assignee = $clientData['assignee'] ?? null;
            $client->lead_quality = $clientData['lead_quality'] ?? null;
            $client->comments_note = $clientData['comments_note'] ?? null;
            $client->married_partner = $clientData['married_partner'] ?? null;
            $client->followers = $clientData['followers'] ?? null;
            $client->tagname = $clientData['tagname'] ?? null;
            $client->related_files = $clientData['related_files'] ?? null;
            $client->applications = $clientData['applications'] ?? null;
            
            // Other
            $client->naati_py = $clientData['naati_py'] ?? null;
            // naati_test, naati_date, nati_language, py_test, py_date, py_field removed - columns don't exist in bansalcrm2 database
            $client->total_points = $clientData['total_points'] ?? null;
            $client->source = $clientData['source'] ?? null;
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
            
            // System Fields
            $client->office_id = $clientData['office_id'] ?? Auth::user()->office_id ?? null;
            // Fix: Use null coalescing operator consistently to avoid "Undefined array key" error
            $verifiedValue = $clientData['verified'] ?? 0;
            $client->verified = is_numeric($verifiedValue) ? (int)$verifiedValue : 0;
            
            $showDashboardValue = $clientData['show_dashboard_per'] ?? 0;
            $client->show_dashboard_per = is_numeric($showDashboardValue) ? (int)$showDashboardValue : 0;
            
            // Note: Archive status (is_archived, archived_by) is NOT imported - imported clients start fresh (not archived)
            // Set default value: imported clients are not archived
            $client->is_archived = 0;
            
            // Client ID (if provided, otherwise will be generated)
            if (!empty($clientData['client_id'])) {
                $client->client_id = $clientData['client_id'];
            }
            
            $client->save();
            $newClientId = $client->id;

            // Generate client_id after saving if not provided (format: FIRSTNAME + YYMM + ID)
            if (empty($client->client_id)) {
                $first_name = $clientData['first_name'] ?? 'CLIENT';
                $client->client_id = strtoupper($first_name) . date('ym') . $newClientId;
                $client->save();
            }

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
                            'user_id' => Auth::id(), // Note: client_phones table uses user_id, not admin_id
                            'contact_type' => $contactData['contact_type'] ?? null,
                            'client_country_code' => $contactData['country_code'] ?? null,
                            'client_phone' => $contactData['phone'] ?? null,
                            // Note: is_verified and verified_at columns don't exist in client_phones table
                        ]);
                    }
                }
            }

            // Import emails - bansalcrm2 stores emails in admins table, not separate client_emails table
            if (isset($importData['emails']) && is_array($importData['emails'])) {
                foreach ($importData['emails'] as $emailData) {
                    // Update primary email if it's the main email
                    if (!empty($emailData['email']) && empty($client->email)) {
                        $client->email = $emailData['email'];
                        $client->email_type = $emailData['email_type'] ?? $client->email_type;
                        // Handle email verification
                        if (isset($emailData['is_verified']) && $emailData['is_verified']) {
                            $client->manual_email_phone_verified = 1;
                        }
                        if (!empty($emailData['verified_at'])) {
                            $client->email_verified_at = $this->parseDateTime($emailData['verified_at']);
                        }
                        $client->save();
                    }
                    // Update additional email if it's the att_email
                    elseif (!empty($emailData['email']) && empty($client->att_email) && $emailData['email'] !== $client->email) {
                        $client->att_email = $emailData['email'];
                        $client->save();
                    }
                }
            }

            // Import passport - bansalcrm2 stores passport in admins table, not separate client_passport_informations table
            if (!empty($importData['passport']) && is_array($importData['passport'])) {
                if (empty($client->passport_number) && !empty($importData['passport']['passport_number'])) {
                    $client->passport_number = $importData['passport']['passport_number'] ?? $importData['passport']['passport'] ?? null;
                }
                if (empty($client->country_passport) && !empty($importData['passport']['passport_country'])) {
                    $client->country_passport = $importData['passport']['passport_country'] ?? null;
                }
                // Note: passport_issue_date and passport_expiry_date are not stored in bansalcrm2 admins table
                $client->save();
            }

            // Import travel information - bansalcrm2 doesn't have client_travel_informations table
            // Skip travel import - table doesn't exist

            // Import visa countries - bansalcrm2 stores visa data in admins table, not separate client_visa_countries table
            if (isset($importData['visa_countries']) && is_array($importData['visa_countries']) && !empty($importData['visa_countries'])) {
                // Use first visa entry to populate admins table fields
                $visaData = $importData['visa_countries'][0];
                if (empty($client->visa_type)) {
                    // Resolve visa type: prefer portable nick_name/title from migrationmanager2 exports,
                    // fall back to visa_type for backwards compatibility with older exports.
                    // bansalcrm2 stores visa type as name string, not numeric ID.
                    $resolvedVisaType = $this->resolveVisaTypeName($visaData);
                    if (!empty($resolvedVisaType)) {
                        $client->visa_type = $resolvedVisaType;
                    }
                }
                if (empty($client->visa_opt) && !empty($visaData['visa_description'])) {
                    $client->visa_opt = $visaData['visa_description'];
                }
                if (empty($client->visaexpiry) && !empty($visaData['visa_expiry_date'])) {
                    $client->visaexpiry = $this->parseDate($visaData['visa_expiry_date']);
                }
                // Note: visa_grant_date is not stored in bansalcrm2 admins table
                $client->save();
            }

            // Import character information - bansalcrm2 doesn't have client_characters table
            // Skip character import - table doesn't exist

            // Import test scores (client_testscore - match migrationmanager2)
            if (isset($importData['test_scores']) && is_array($importData['test_scores']) && class_exists(\App\Models\ClientTestScore::class)) {
                foreach ($importData['test_scores'] as $testData) {
                    \App\Models\ClientTestScore::create([
                        'client_id' => $newClientId,
                        'admin_id' => null,
                        'test_type' => $testData['test_type'] ?? null,
                        'listening' => $testData['listening'] ?? null,
                        'reading' => $testData['reading'] ?? null,
                        'writing' => $testData['writing'] ?? null,
                        'speaking' => $testData['speaking'] ?? null,
                        'overall_score' => $testData['overall_score'] ?? null,
                        'test_date' => $this->parseDate($testData['test_date'] ?? null),
                        'relevant_test' => 1,
                    ]);
                }
            }

            // Import services (bansalcrm2 specific - client_service_takens table)
            if (isset($importData['services']) && is_array($importData['services']) && class_exists(\App\Models\clientServiceTaken::class)) {
                foreach ($importData['services'] as $serviceData) {
                    $serviceRecord = [
                        'client_id' => $newClientId,
                        'service_type' => $serviceData['service_type'] ?? null,
                    ];
                    
                    // Add migration-specific fields
                    if ($serviceData['service_type'] === 'Migration') {
                        $serviceRecord['mig_ref_no'] = $serviceData['mig_ref_no'] ?? null;
                        $serviceRecord['mig_service'] = $serviceData['mig_service'] ?? null;
                        $serviceRecord['mig_notes'] = $serviceData['mig_notes'] ?? null;
                    }
                    
                    // Add education-specific fields
                    if ($serviceData['service_type'] === 'Education') {
                        $serviceRecord['edu_course'] = $serviceData['edu_course'] ?? null;
                        $serviceRecord['edu_college'] = $serviceData['edu_college'] ?? null;
                        $serviceRecord['edu_service_start_date'] = $serviceData['edu_service_start_date'] ?? null;
                        $serviceRecord['edu_notes'] = $serviceData['edu_notes'] ?? null;
                    }
                    
                    \App\Models\clientServiceTaken::create($serviceRecord);
                }
            }

            // Import activities - bansalcrm2 activities_logs table structure
            if (isset($importData['activities']) && is_array($importData['activities'])) {
                foreach ($importData['activities'] as $activityData) {
                    $activityCreatedAt = $this->parseDateTime($activityData['created_at'] ?? null);
                    
                    // Fix: Use null coalescing operator consistently to avoid "Undefined array key" error
                    $taskStatusValue = $activityData['task_status'] ?? 0;
                    $pinValue = $activityData['pin'] ?? 0;
                    
                    ActivitiesLog::create([
                        'client_id' => $newClientId,
                        'created_by' => $activityData['created_by'] ?? Auth::id(),
                        'subject' => $activityData['subject'] ?? 'Imported Activity',
                        'description' => $activityData['description'] ?? null,
                        'use_for' => $activityData['use_for'] ?? null,
                        'task_status' => is_numeric($taskStatusValue) ? (int)$taskStatusValue : 0,
                        'pin' => is_numeric($pinValue) ? (int)$pinValue : 0,
                        'created_at' => $activityCreatedAt ?? now(),
                        'updated_at' => $activityCreatedAt ?? now(),
                        // Note: activity_type, followup_date, task_group columns don't exist in bansalcrm2 activities_logs table
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

    /**
     * Resolve visa type name from visa data.
     * 
     * bansalcrm2 stores visa type as a name string (e.g., "600 - Visitor - Outside Australia"),
     * while migrationmanager2 exports include:
     * - visa_type: numeric Matter ID (not useful for bansalcrm2)
     * - visa_type_matter_nick_name: portable identifier
     * - visa_type_matter_title: human-readable title
     * 
     * Priority:
     * 1. visa_type_matter_nick_name (most portable cross-system identifier)
     * 2. visa_type_matter_title (human-readable fallback)
     * 3. visa_type if it's a non-numeric string (backwards compat with older/same-system exports)
     * 
     * @param array $visaData
     * @return string|null
     */
    private function resolveVisaTypeName(array $visaData)
    {
        // Prefer nick_name (most portable cross-system identifier)
        $nickName = $visaData['visa_type_matter_nick_name'] ?? null;
        if (is_string($nickName) && $nickName !== '') {
            return $nickName;
        }

        // Fall back to title (human-readable)
        $title = $visaData['visa_type_matter_title'] ?? null;
        if (is_string($title) && $title !== '') {
            return $title;
        }

        // Fall back to visa_type for backwards compatibility
        // Only use if it's a non-empty string that doesn't look like a pure numeric ID
        $visaType = $visaData['visa_type'] ?? null;
        if ($visaType !== null && $visaType !== '') {
            // If it's already a name string (not purely numeric), use it
            // This handles older bansalcrm2 exports where visa_type is already the name
            if (!is_numeric($visaType)) {
                return $visaType;
            }
            // If it's numeric, it's likely a Matter ID from migrationmanager2
            // We can't resolve it without the portable fields, so log a warning
            Log::warning('Visa import: numeric visa_type without portable fields', [
                'visa_type' => $visaType,
                'hint' => 'Export may be from migrationmanager2 without visa_type_matter_title/nick_name'
            ]);
        }

        return null;
    }

    /**
     * Extract phone numbers from import data
     * Checks both the contacts array and the client's direct phone fields
     * 
     * @param array $importData
     * @return array Array of phone numbers with country_code and phone
     */
    private function extractPhoneNumbersFromImport(array $importData): array
    {
        $phoneNumbers = [];

        // Extract from contacts array
        if (isset($importData['contacts']) && is_array($importData['contacts'])) {
            foreach ($importData['contacts'] as $contact) {
                if (!empty($contact['phone'])) {
                    $phoneNumbers[] = [
                        'country_code' => $contact['country_code'] ?? null,
                        'phone' => $contact['phone']
                    ];
                }
            }
        }

        // Extract from client's direct phone fields (if present)
        if (isset($importData['client'])) {
            $clientData = $importData['client'];
            
            // Check main phone field
            if (!empty($clientData['phone'])) {
                $phoneNumbers[] = [
                    'country_code' => $clientData['country_code'] ?? null,
                    'phone' => $clientData['phone']
                ];
            }
            
            // Check att_phone (attendant phone)
            if (!empty($clientData['att_phone'])) {
                $phoneNumbers[] = [
                    'country_code' => $clientData['att_country_code'] ?? null,
                    'phone' => $clientData['att_phone']
                ];
            }
        }

        return $phoneNumbers;
    }

    /**
     * Normalize phone number for comparison
     * Removes spaces, dashes, parentheses, and other formatting
     * 
     * @param string|null $phone
     * @return string Normalized phone number
     */
    private function normalizePhoneNumber(?string $phone): string
    {
        if (empty($phone)) {
            return '';
        }

        // Remove all non-digit characters except + (though + should be in country code, not phone)
        return preg_replace('/[^\d]/', '', trim($phone));
    }
}
