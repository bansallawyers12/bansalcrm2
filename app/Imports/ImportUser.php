<?php
namespace App\Imports;
// NOTE: User model/table has been removed - this import class uses Agent model instead
// use App\Models\User;
use App\Models\Agent;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Http\Request;

class ImportUser implements ToModel
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function model(array $row)
    {
        if($this->request->struture == 'Individual'){
            $full_name = $row[0];
            $business_name = '';
            $tax_number = '';
            // Set default contract_expiry_date for Individual agents (not in import data)
            $contract_expiry_date = '2099-12-31';
            $country_code = $row[2];
            $phone = $row[3];
            $email = $row[1];
            $address = $row[4];
            $city = $row[5];
            $state = $row[6];
            $zip = $row[7];
            $country = $row[8];
            $income_sharing = $row[10];
            $claim_revenue = $row[9];
        }else{
            $full_name = $row[1];
            $business_name = $row[0];
            $tax_number = $row[2];
            $contract_expiry_date = $row[3];
            $country_code = $row[5];
            $phone = $row[6];
            $email = $row[4];
            $address = $row[7];
            $city = $row[8];
            $state = $row[9];
            $zip = $row[10];
            $country = $row[11];
            $income_sharing = $row[12];
            $claim_revenue = $row[13];
        }
        return new Agent([
            'full_name' => $full_name,
            'agent_type' => implode(',',@$this->request->super_agent),
            'related_office' => $this->request->related_office,
            'struture' => $this->request->struture,
            'business_name' => $business_name,
            'tax_number' => $tax_number,
            'contract_expiry_date' => $contract_expiry_date,
            'country_code' => $country_code,
            'phone' => $phone,
            'email' => $email,
            'address' => $address,
            'city' => $city,
            'state' => $state,
            'zip' => $zip,
            'country' => $country,
            'income_sharing' => $income_sharing,
            'claim_revenue' => $claim_revenue,
        ]);
    }
}