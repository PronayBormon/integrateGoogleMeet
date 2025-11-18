<?php

use App\Http\Controllers\MeetingController;
use App\Models\User;
use App\Models\GoogleToken;
use App\Services\GoogleService;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    $total = User::count();
    $user = User::where('role', 'user')->count();

    $active = User::where('status', 'active')->count();
    $inactive = User::where('status', 'inactive')->count();
    $banned = User::where('status', 'banned')->count();
    return view('backend.layouts.dashboard', compact(
        "total",
        "user",
        "active",
        "inactive",
        "banned"
    ));
})->middleware(['auth:sanctum', 'role:admin,super_admin,manager,editor'])->name('home');

Route::get('/dashboard', function () {

    $total = User::count();
    $user = User::where('role', 'user')->count();

    $active = User::where('status', 'active')->count();
    $inactive = User::where('status', 'inactive')->count();
    $banned = User::where('status', 'banned')->count();
    return view('backend.layouts.dashboard', compact(
        "total",
        "user",
        "active",
        "inactive",
        "banned"
    ));
})->middleware(['auth:sanctum', 'role:admin,super_admin,manager,editor'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });


require __DIR__ . '/auth.php';
require __DIR__ . '/backend.php';


// PUBLIC CALLBACK (NO AUTH HERE)
Route::get('/oauth/google/callback', function (GoogleService $google) {
    $client = $google->client();
    $token = $client->fetchAccessTokenWithAuthCode(request('code'));

    GoogleToken::updateOrCreate(
        ['user_id' => session('google_user_id')], // <-- store temporarily before redirect
        [
            'access_token' => $client->getAccessToken(),
            'refresh_token' => $client->getRefreshToken() ?? null,
            'expires_at' => now()->addSeconds($client->getAccessToken()['expires_in'] ?? 3600)
        ]
    );

    return redirect('/dashboard')->with('success', 'Google connected');
});


// PROTECTED ROUTES
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/meeting', [MeetingController::class, 'meeting'])->name('meetings.index');
    Route::get('/google/connect', function (GoogleService $google) {

        // Save user id before redirecting (because callback is public)
        session(['google_user_id' => auth()->id()]);

        $client = $google->client();
        return redirect()->away($client->createAuthUrl());
    })->name('google.connect');

    Route::post('/meeting', [MeetingController::class, 'store'])->name('meetings.store');
    Route::get('/recordings', function () {
        $recordings = app(\App\Services\GoogleService::class)->listMeetRecordings(auth()->id());

        foreach ($recordings as $rec) {
            echo $rec->getName() . ' - ' . $rec->getId() . '<br>';
        }
    });
});
