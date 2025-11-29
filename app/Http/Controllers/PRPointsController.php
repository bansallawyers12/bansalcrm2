<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Routing\Route;

use Illuminate\Support\Facades\Mail;
use App\Mail\PRPointsEmail;

class PRPointsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

	}

    public function index()
    {
        return view('pr-points');
    }

    public function calculate(Request $request)
    {
        $age = $request->input('age');
        $englishLevel = $request->input('english_level');
        $skillEmployment = $request->input('skill_employment');
        $educationQualification = $request->input('education_qualification');
        $australianStudy = $request->input('australian_study');
        $specialistEducation = $request->input('specialist_education');
        $accreditedInAustralia = $request->input('accredited_in_australia');
        $professionalYear = $request->input('professional_year');

        // Calculate PR points based on the input
        $points = 0;

        // Age
        if ($age >= 18 && $age <= 24) {
            $points += 25;
        } elseif ($age >= 25 && $age <= 32) {
            $points += 30;
        } elseif ($age >= 33 && $age <= 39) {
            $points += 25;
        } elseif ($age >= 40 && $age <= 44) {
            $points += 15;
        }

        // English language skills
        if ($englishLevel === 'competent') {
            $points += 0;
        } elseif ($englishLevel === 'proficient') {
            $points += 10;
        } elseif ($englishLevel === 'superior') {
            $points += 20;
        }

        // Skilled employment
        if ($skillEmployment === 'one_year') {
            $points += 5;
        } elseif ($skillEmployment === 'three_years') {
            $points += 10;
        } elseif ($skillEmployment === 'five_years') {
            $points += 15;
        } elseif ($skillEmployment === 'eight_years') {
            $points += 20;
        }

        // Educational qualifications
        if ($educationQualification === 'doctorate') {
            $points += 20;
        } elseif ($educationQualification === 'bachelor') {
            $points += 15;
        } elseif ($educationQualification === 'diploma') {
            $points += 10;
        }

        // Australian study requirement
        if ($australianStudy === 'yes') {
            $points += 5;
        }

        // Specialist education qualification
        if ($specialistEducation === 'yes') {
            $points += 10;
        }

        // Accredited in a community language
        if ($accreditedInAustralia === 'yes') {
            $points += 5;
        }

        // Professional year in Australia
        if ($professionalYear === 'yes') {
            $points += 5;
        }

        $emailAddress = $request->input('email');

        // Send email with the points
        Mail::to($emailAddress)->send(new PRPointsEmail($points));

        return view('pr-points-result', ['points' => $points]);
    }
}
