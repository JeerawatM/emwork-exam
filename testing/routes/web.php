<?php
use App\Http\Controllers\LeaveRequestController;
use Illuminate\Support\Facades\Route;
 // อย่าลืม import Controller
// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return redirect()->route('leave_requests.index');
});

Route::get('/temple', function () {
    return view('temple_display');
});

// Resources routes for CRUD operations
Route::resource('leave_requests', LeaveRequestController::class)->except(['show', 'edit', 'update']);

// Custom route for updating status (since we're not using the default update method for this)
Route::post('leave_requests/{leaveRequest}/status', [LeaveRequestController::class, 'updateStatus'])->name('leave_requests.update_status');