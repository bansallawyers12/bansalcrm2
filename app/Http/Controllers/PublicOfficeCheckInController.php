<?php

namespace App\Http\Controllers;

use App\Services\PublicOfficeCheckInService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PublicOfficeCheckInController extends Controller
{
    public function __construct(private readonly PublicOfficeCheckInService $checkIns) {}

    public function index(): View
    {
        return view('public.office_check_in', [
            'visitReasons' => PublicOfficeCheckInService::visitReasons(),
            'heardAboutOptions' => PublicOfficeCheckInService::heardAboutOptions(),
            'qualificationLevels' => PublicOfficeCheckInService::qualificationLevels(),
            'currentVisaOptions' => PublicOfficeCheckInService::currentVisaOptions(),
        ]);
    }

    public function lookup(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => $this->checkIns->australianPhoneValidationRules(),
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        return response()->json([
            'matches' => $this->checkIns->matches($validated['phone'], $validated['email'] ?? null),
        ]);
    }

    public function submit(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => $this->checkIns->australianPhoneValidationRules(),
            'email' => ['nullable', 'email', 'max:255'],
            'admin_id' => ['nullable', 'integer', 'min:1'],
            'admin_type' => ['nullable', 'string', Rule::in(['client', 'lead'])],
            'walk_in_name' => ['nullable', 'string', 'max:140'],
            'visit_reason' => ['nullable', 'string', 'max:100', Rule::in(array_keys(PublicOfficeCheckInService::visitReasons()))],
            'visit_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $result = $this->checkIns->submit($validated, $request->ip());
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable) {
            return response()->json([
                'success' => false,
                'message' => 'We could not save your check-in. Please try again or see reception.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
        ]);
    }

    public function createLead(Request $request): JsonResponse
    {
        $validated = $request->validate($this->checkIns->leadDetailRules());

        try {
            $result = $this->checkIns->createLead($validated, $request->ip());
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable) {
            return response()->json([
                'success' => false,
                'message' => 'We could not save your check-in. Please try again or see reception.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => $result['message'],
        ]);
    }
}
