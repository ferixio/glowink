<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking migration results...\n";

// Cek user GO979A0001
$user = DB::table('users')->where('id_mitra', 'GO979A0001')->first();
if ($user) {
    echo "User GO979A0001 found with ID: " . $user->id . "\n";
    
    // Cek downline level 1
    $level1 = DB::table('jaringan_mitras')->where('sponsor_id', $user->id)->where('level', 1)->count();
    echo "Level 1 downlines: " . $level1 . "\n";
    
    // Cek semua level
    $allLevels = DB::table('jaringan_mitras')->where('sponsor_id', $user->id)
        ->select('level', DB::raw('COUNT(*) as count'))
        ->groupBy('level')
        ->orderBy('level')
        ->get();
    
    echo "All levels:\n";
    foreach ($allLevels as $level) {
        echo "Level " . $level->level . ": " . $level->count . " downlines\n";
    }
    
    // Cek total downlines
    $totalDownlines = DB::table('jaringan_mitras')->where('sponsor_id', $user->id)->count();
    echo "Total downlines: " . $totalDownlines . "\n";
    
    // Cek distribusi level secara keseluruhan
    echo "\nOverall level distribution:\n";
    $overallLevels = DB::table('jaringan_mitras')
        ->select('level', DB::raw('COUNT(*) as count'))
        ->groupBy('level')
        ->orderBy('level')
        ->get();
    
    foreach ($overallLevels as $level) {
        echo "Level " . $level->level . ": " . $level->count . " relations\n";
    }
    
} else {
    echo "User GO979A0001 not found!\n";
}

echo "\nTotal data in jaringan_mitras: " . DB::table('jaringan_mitras')->count() . "\n";
