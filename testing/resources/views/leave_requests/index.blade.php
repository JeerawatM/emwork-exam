{{-- resources/views/leave_requests/index.blade.php --}}
<x-app-layout>
    <div x-data="{ showCreateModal: false, showStatusModal: false, selectedLeaveId: null, currentStatus: 'pending' }">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">รายการขอลาหยุด</h2>
            <button @click="showCreateModal = true" style="background-color: rgb(0, 0, 255)" class="hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                <span>เพิ่มรายการขอลา</span>
            </button>
        </div>

        {{-- Search and Sort Form --}}
        <div class="mb-6 bg-gray-50 p-4 rounded-lg shadow-sm">
            <form action="{{ route('leave_requests.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
                <div class="flex-grow">
                    <label for="search" class="sr-only">ค้นหา</label>
                    <input type="text" name="search" id="search" placeholder="ค้นหาด้วยชื่อ-นามสกุล หรือวันที่ (YYYY-MM-DD)"
                           value="{{ request('search') }}"
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label for="sort_by" class="sr-only">เรียงตาม</label>
                    <select name="sort_by" id="sort_by" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="created_at" {{ $sortBy === 'created_at' ? 'selected' : '' }}>วันเวลาที่บันทึก</option>
                        <option value="start_date" {{ $sortBy === 'start_date' ? 'selected' : '' }}>วันที่ลา</option>
                        <option value="full_name" {{ $sortBy === 'full_name' ? 'selected' : '' }}>ชื่อ-นามสกุล</option>
                    </select>
                </div>
                <div>
                    <label for="sort_order" class="sr-only">ลำดับ</label>
                    <select name="sort_order" id="sort_order" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="desc" {{ $sortOrder === 'desc' ? 'selected' : '' }}>มากไปน้อย</option>
                        <option value="asc" {{ $sortOrder === 'asc' ? 'selected' : '' }}>น้อยไปมาก</option>
                    </select>
                </div>
                <button type="submit" style="background-color: rgb(4, 0, 218)" class="bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-md transition-colors">
                    ค้นหา / เรียงลำดับ
                </button>
                <a href="{{ route('leave_requests.index') }}" style="background-color: rgb(228, 228, 228)"  class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md transition-colors">
                    ล้างค่า
                </a>
            </form>
        </div>

        {{-- List of Leave Requests --}}
        @if ($leaveRequests->isEmpty())
            <p class="text-center text-gray-600 py-8">ยังไม่มีรายการขอลาหยุด</p>
        @else
            <div class="overflow-x-auto bg-white shadow-md rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ชื่อ-นามสกุล</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สังกัด/ตำแหน่ง</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ประเภทการลา</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">วันที่ลา</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สถานะ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">บันทึกเมื่อ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($leaveRequests as $request)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->full_name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->department_position ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @php
                                        $leaveTypeMap = [
                                            'sick_leave' => 'ลาป่วย',
                                            'personal_leave' => 'ลากิจ',
                                            'annual_leave' => 'พักร้อน',
                                            'other' => 'อื่นๆ',
                                        ];
                                    @endphp
                                    {{ $leaveTypeMap[$request->leave_type] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Carbon\Carbon::parse($request->start_date)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($request->end_date)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if ($request->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif ($request->status === 'approved') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800 @endif">
                                        @php
                                            $statusMap = [
                                                'pending' => 'รอพิจารณา',
                                                'approved' => 'อนุมัติ',
                                                'rejected' => 'ไม่อนุมัติ',
                                            ];
                                        @endphp
                                        {{ $statusMap[$request->status] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $request->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <button @click="showStatusModal = true; selectedLeaveId = {{ $request->id }}; currentStatus = '{{ $request->status }}'"
                                                class="text-blue-600 hover:text-blue-900 p-1 rounded hover:bg-blue-50
                                                {{ $request->status !== 'pending' ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                {{ $request->status !== 'pending' ? 'disabled' : '' }}
                                                title="{{ $request->status !== 'pending' ? 'สามารถปรับสถานะได้เฉพาะรายการที่ "รอพิจารณา" เท่านั้น' : 'ปรับสถานะ' }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                            </svg>
                                        </button>
                                        <form action="{{ route('leave_requests.destroy', $request) }}" method="POST" onsubmit="return confirm('คุณแน่ใจที่จะลบรายการขอลาหยุดนี้?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 p-1 rounded hover:bg-red-50">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $leaveRequests->links() }}
            </div>
        @endif

        {{-- Create Leave Request Modal --}}
        <div x-show="showCreateModal" style={'background-color:"red"'} class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div @click.away="showCreateModal = false" class="bg-white rounded-lg p-6 w-full max-w-md shadow-xl">
                <h2 class="text-xl font-bold mb-4">บันทึกรายการขอลาหยุด</h2>
                <form method="POST" action="{{ route('leave_requests.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="full_name" class="block text-gray-700 text-sm font-bold mb-2">ชื่อ - นามสกุล:<span class="text-red-500">*</span></label>
                        <input type="text" id="full_name" name="full_name" required value="{{ old('full_name') }}"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label for="department_position" class="block text-gray-700 text-sm font-bold mb-2">สังกัด/ตำแหน่ง:</label>
                        <input type="text" id="department_position" name="department_position" value="{{ old('department_position') }}"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">อีเมล์:</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label for="phone_number" class="block text-gray-700 text-sm font-bold mb-2">เบอร์โทรศัพท์:<span class="text-red-500">*</span></label>
                        <input type="text" id="phone_number" name="phone_number" required value="{{ old('phone_number') }}"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label for="leave_type" class="block text-gray-700 text-sm font-bold mb-2">ประเภทการลา:<span class="text-red-500">*</span></label>
                        <select id="leave_type" name="leave_type" required
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="sick_leave" {{ old('leave_type') == 'sick_leave' ? 'selected' : '' }}>ลาป่วย</option>
                            <option value="personal_leave" {{ old('leave_type') == 'personal_leave' ? 'selected' : '' }}>ลากิจ</option>
                            <option value="annual_leave" {{ old('leave_type') == 'annual_leave' ? 'selected' : '' }}>พักร้อน</option>
                            <option value="other" {{ old('leave_type') == 'other' ? 'selected' : '' }}>อื่นๆ</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="reason" class="block text-gray-700 text-sm font-bold mb-2">สาเหตุการลา:<span class="text-red-500">*</span></label>
                        <textarea id="reason" name="reason" rows="3" required
                                  class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('reason') }}</textarea>
                    </div>
                    <div class="mb-4">
                        <label for="start_date" class="block text-gray-700 text-sm font-bold mb-2">วันที่ขอลา (เริ่มต้น):<span class="text-red-500">*</span></label>
                        <input type="date" id="start_date" name="start_date" required value="{{ old('start_date', \Carbon\Carbon::now()->format('Y-m-d')) }}"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-6">
                        <label for="end_date" class="block text-gray-700 text-sm font-bold mb-2">ถึงวันที่ (สิ้นสุด):<span class="text-red-500">*</span></label>
                        <input type="date" id="end_date" name="end_date" required value="{{ old('end_date', \Carbon\Carbon::now()->format('Y-m-d')) }}"
                               class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="showCreateModal = false"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition-colors">
                            ยกเลิก
                        </button>
                        <button type="submit" style="background-color: blue" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors">
                            บันทึก
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Update Status Modal --}}
        <div x-show="showStatusModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div @click.away="showStatusModal = false" class="bg-white rounded-lg p-6 w-full max-w-sm shadow-xl">
                <h2 class="text-xl font-bold mb-4">ปรับสถานะการพิจารณา</h2>
                <form :action="'/leave_requests/' + selectedLeaveId + '/status'" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="status" class="block text-gray-700 text-sm font-bold mb-2">สถานะ:</label>
                        <select x-model="currentStatus" id="status" name="status" required
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="pending" disabled>รอพิจารณา</option> {{-- Cannot change from pending to pending --}}
                            <option value="approved">อนุมัติ</option>
                            <option value="rejected">ไม่อนุมัติ</option>
                        </select>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" @click="showStatusModal = false"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-lg transition-colors">
                            ยกเลิก
                        </button>
                        <button type="submit" style="background-color: blue" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors">
                            บันทึกสถานะ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>