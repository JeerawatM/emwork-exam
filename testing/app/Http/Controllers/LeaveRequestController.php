<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
class LeaveRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = LeaveRequest::query();

        // C. สามารถค้นหารายการตาม ชื่อ - นามสกุล, วันที่ขอลา ได้
        if ($search = $request->input('search')) {
            $query->where('full_name', 'like', "%{$search}%")
                ->orWhereDate('start_date', $search)
                ->orWhereDate('end_date', $search);
        }

        // C. สามารถเรียงลำดับจาก วันเวลาที่บันทึก จากมากไปน้อย - น้อยไปมากได้
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        $query->orderBy($sortBy, $sortOrder);

        $leaveRequests = $query->paginate(10); // เพิ่มการแบ่งหน้า

        return view('leave_requests.index', compact('leaveRequests', 'sortBy', 'sortOrder'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('leave_requests.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'department_position' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'required|string|max:20',
            'leave_type' => ['required', Rule::in(['sick_leave', 'personal_leave', 'annual_leave', 'other'])],
            'reason' => 'required|string',
            'start_date' => 'required|date|before_or_equal:end_date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $today = Carbon::now()->startOfDay(); // วันที่ปัจจุบัน

        Log::info('Leave Request Debug Log:', [
        'Current DateTime (Carbon::now()->startOfDay())' => $today->toDateTimeString(),
        'Start Date (from form)' => $startDate->toDateTimeString(),
        'End Date (from form)' => $endDate->toDateTimeString(),
        'Difference in Days (start_date vs today)' => $startDate->diffInDays($today, false),
        'Difference in Days (start_date vs end_date)' => $startDate->diffInDays($endDate), // สำหรับเช็คว่าลากี่วัน
        'Is start_date before today?' => $startDate->isBefore($today),
        'Leave Type' => $request->leave_type,
    ]);

        // B. ไม่อนุญาติให้บันทึกวันลาย้อนหลัง
    if ($startDate->isBefore($today)) {
        return back()->withInput()->withErrors(['date_error' => 'ไม่อนุญาตให้บันทึกวันลาย้อนหลัง']);
    }

    // B. กรณีพักร้อน
    if ($request->leave_type === 'annual_leave') {
        // B. กรณีพักร้อนลาล่วงหน้าอย่างน้อย 3 วัน
        // แก้ไข Logic ตรงนี้ให้ถูกต้อง
        // เราต้องการนับวันจาก "วันนี้" ไปถึง "วันเริ่มลา"
        $daysInAdvance = $today->diffInDays($startDate, false); // นับจาก today ไป startDate

        if ($daysInAdvance < 3) { // ถ้าวันล่วงหน้าน้อยกว่า 3 วัน
            return back()->withInput()->withErrors(['date_error' => 'การลาพักร้อนต้องลาล่วงหน้าอย่างน้อย 3 วัน']);
        }

        // B. กรณีพักร้อนลาติดต่อกันได้ไม่เกิน 2 วัน
        // สำหรับ diffInDays($endDate) โดยไม่มี false parameter จะนับจำนวนวันเต็มๆ ระหว่างสองวัน
        // 0 วัน คือ วันเดียวกัน
        // 1 วัน คือ วันถัดไป (เช่น 25-26)
        // ดังนั้น ถ้า diffInDays >= 2 หมายถึงลา 3 วันขึ้นไป (เช่น 25-27, 25-28)
        if ($startDate->diffInDays($endDate) >= 2) {
            return back()->withInput()->withErrors(['date_error' => 'การลาพักร้อนสามารถลาติดต่อกันได้ไม่เกิน 2 วัน']);
        }
    }
        LeaveRequest::create($request->all());

        return redirect()->route('leave_requests.index')->with('success', 'บันทึกรายการขอลาหยุดสำเร็จ!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LeaveRequest $leaveRequest)
    {
        $leaveRequest->delete();

        return redirect()->route('leave_requests.index')->with('success', 'ลบรายการขอลาหยุดสำเร็จ!');
    }

    /**
     * Update the status of the specified resource.
     */
    public function updateStatus(Request $request, LeaveRequest $leaveRequest)
    {
        // C. สามารถปรับสถานะการพิจารณาได้เฉพาะสถานะ “รอพิจารณา” เท่านั้น
        if ($leaveRequest->status !== 'pending') {
            return back()->with('error', 'ไม่สามารถปรับสถานะรายการที่ไม่ได้อยู่ในสถานะ "รอพิจารณา" ได้');
        }

        $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
        ]);

        $leaveRequest->update([
            'status' => $request->status,
        ]);

        return redirect()->route('leave_requests.index')->with('success', 'ปรับสถานะการพิจารณาสำเร็จ!');
    }
}
