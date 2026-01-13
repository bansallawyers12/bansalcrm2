<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AddressController extends Controller
{
    /**
     * Search addresses using Google Places Autocomplete API
     */
    public function searchAddress(Request $request)
    {
        $query = $request->input('query');
        $apiKey = env('GOOGLE_MAPS_API_KEY');
        
        if (!$apiKey || strlen($query) < 3) {
            return response()->json([
                'status' => 'ERROR',
                'predictions' => []
            ]);
        }
        
        $url = 'https://maps.googleapis.com/maps/api/place/autocomplete/json';
        
        $params = http_build_query([
            'input' => $query,
            'key' => $apiKey,
            'types' => 'address',
            'components' => 'country:au', // Restrict to Australia (change as needed)
        ]);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For local dev
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($curlError) {
            \Log::error('Google Places API Error: ' . $curlError);
            return response()->json([
                'status' => 'ERROR',
                'predictions' => []
            ]);
        }
        
        $data = json_decode($response, true);
        
        if ($httpCode === 200 && isset($data['status']) && $data['status'] === 'OK') {
            return response()->json($data);
        }
        
        return response()->json([
            'status' => 'ERROR',
            'predictions' => []
        ]);
    }
    
    /**
     * Get place details including address components
     */
    public function getPlaceDetails(Request $request)
    {
        $placeId = $request->input('place_id');
        $apiKey = env('GOOGLE_MAPS_API_KEY');
        
        if (!$apiKey || !$placeId) {
            return response()->json([
                'status' => 'ERROR',
                'result' => []
            ]);
        }
        
        $url = 'https://maps.googleapis.com/maps/api/place/details/json';
        
        $params = http_build_query([
            'place_id' => $placeId,
            'key' => $apiKey,
            'fields' => 'address_components,formatted_address,name'
        ]);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url . '?' . $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($curlError) {
            \Log::error('Google Places Details API Error: ' . $curlError);
        }
        
        $data = json_decode($response, true);
        
        if ($httpCode === 200 && isset($data['status']) && $data['status'] === 'OK') {
            return response()->json($data);
        }
        
        return response()->json([
            'status' => 'OK',
            'result' => [
                'formatted_address' => $request->input('description', ''),
                'address_components' => []
            ]
        ]);
    }
}
