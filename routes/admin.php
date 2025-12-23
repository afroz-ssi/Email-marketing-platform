<?php

use App\Http\Controllers\Admin\{CampaignController, HomeController, LeadController, OutlookAccountController};
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Common\CommonController;
use App\Http\Controllers\ScraperController;

Route::any('forgot-password', [CommonController::class, 'forgotPassword'])->name('forgotPassword');
Route::any('otp-validations', [CommonController::class, 'otpValidations'])->name('otpValidations');
Route::any('reset-password', [CommonController::class, 'resetPassword'])->name('resetPassword');
Route::get('login', [HomeController::class, 'index'])->name('login');
Route::post('login', [HomeController::class, 'authenticate'])->name('login-submit');

Route::middleware(['customAuth', 'isAdmin'])->group(function () {
    Route::get('dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    Route::post('logout', [HomeController::class, 'logout'])->name('logout');

    /** Lead */
    Route::get('leads', [LeadController::class, 'index'])->name('leads');
    Route::any('create-lead', [LeadController::class, 'create'])->name('createLead');
    Route::any('edit-lead/{id}', [LeadController::class, 'edit'])->name('editLead');
    Route::delete('delete-lead/{id}', [LeadController::class, 'destroy'])->name('deleteLead');
    Route::get('show-lead/{id}', [LeadController::class, 'show'])->name('showLead');


    Route::post('/scrape', [ScraperController::class, 'scrape'])->name('scrape');
    Route::post('/bulk-scrape', [ScraperController::class, 'bulkScrape'])->name('bulkScrape');

    Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns');
    Route::get('/campaigns/analytics', [CampaignController::class, 'analytics'])->name('campaignAnalytics');
    Route::any('/create-campaign', [CampaignController::class, 'createCampaign'])->name('createCampaign');
    Route::any('edit-campaign/{id}', [CampaignController::class, 'editCampaign'])->name('editCampaign');
    Route::delete('delete-campaign/{id}', [CampaignController::class, 'deleteCampaign'])->name('deleteCampaign');
    Route::post('reset-all-accounts', [CampaignController::class, 'resetAllAccounts'])->name('resetAllAccounts');
    Route::post('/campaigns/{campaign}/send', [CampaignController::class, 'send'])->name('sendCampaign');

    /** Outlook Account */
    Route::get('outlook-accounts', [OutlookAccountController::class, 'index'])->name('outlookAccounts');
    Route::any('create-outlook-account', [OutlookAccountController::class, 'create'])->name('createOutlookAccount');
    Route::any('edit-outlook-account/{id}', [OutlookAccountController::class, 'edit'])->name('editOutlookAccount');
    Route::delete('delete-outlook-account/{id}', [OutlookAccountController::class, 'destroy'])->name('deleteOutlookAccount');
    Route::post('reset-sent-count/{id}', [OutlookAccountController::class, 'resetSentCount'])->name('resetSentCount');
});
