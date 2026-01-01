<?php

use Illuminate\Support\Facades\Route;
use App\Models\InvoiceSchedule;

Route::get('/test-memory', function() {
    ini_set('memory_limit', '2048M');
    
    echo "Memory at start: " . memory_get_usage(true) / 1024 / 1024 . " MB<br>";
    
    // Test 1: Simple query
    try {
        $count = InvoiceSchedule::count();
        echo "Test 1 PASSED: Found {$count} invoice schedules<br>";
        echo "Memory after count: " . memory_get_usage(true) / 1024 / 1024 . " MB<br>";
    } catch (\Exception $e) {
        echo "Test 1 FAILED: " . $e->getMessage() . "<br>";
    }
    
    // Test 2: Get all without eager loading
    try {
        $schedules = InvoiceSchedule::orderby('id','desc')->take(5)->get();
        echo "Test 2 PASSED: Loaded " . $schedules->count() . " schedules without eager loading<br>";
        echo "Memory after simple get: " . memory_get_usage(true) / 1024 / 1024 . " MB<br>";
    } catch (\Exception $e) {
        echo "Test 2 FAILED: " . $e->getMessage() . "<br>";
    }
    
    // Test 3: With eager loading
    try {
        $schedules = InvoiceSchedule::with([
            'scheduleItems'
        ])->orderby('id','desc')->take(5)->get();
        echo "Test 3 PASSED: Loaded with scheduleItems<br>";
        echo "Memory after scheduleItems: " . memory_get_usage(true) / 1024 / 1024 . " MB<br>";
    } catch (\Exception $e) {
        echo "Test 3 FAILED: " . $e->getMessage() . "<br>";
    }
    
    // Test 4: With client
    try {
        $schedules = InvoiceSchedule::with(['client'])->orderby('id','desc')->take(5)->get();
        echo "Test 4 PASSED: Loaded with client<br>";
        echo "Memory after client: " . memory_get_usage(true) / 1024 / 1024 . " MB<br>";
    } catch (\Exception $e) {
        echo "Test 4 FAILED: " . $e->getMessage() . "<br>";
    }
    
    // Test 5: With application
    try {
        $schedules = InvoiceSchedule::with(['application'])->orderby('id','desc')->take(5)->get();
        echo "Test 5 PASSED: Loaded with application<br>";
        echo "Memory after application: " . memory_get_usage(true) / 1024 / 1024 . " MB<br>";
    } catch (\Exception $e) {
        echo "Test 5 FAILED: " . $e->getMessage() . "<br>";
    }
    
    // Test 6: Full eager loading
    try {
        $schedules = InvoiceSchedule::with([
            'client',
            'application.product',
            'application.partner',
            'application.branch',
            'application.workflow',
            'scheduleItems'
        ])->orderby('id','desc')->take(5)->get();
        echo "Test 6 PASSED: Full eager loading worked!<br>";
        echo "Memory after full eager loading: " . memory_get_usage(true) / 1024 / 1024 . " MB<br>";
        echo "Peak memory: " . memory_get_peak_usage(true) / 1024 / 1024 . " MB<br>";
    } catch (\Exception $e) {
        echo "Test 6 FAILED: " . $e->getMessage() . "<br>";
        echo "Memory at failure: " . memory_get_usage(true) / 1024 / 1024 . " MB<br>";
    }
    
    echo "<br>All tests completed!";
});



