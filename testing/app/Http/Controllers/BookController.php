<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book; // อย่าลืม import Model Book

class BookController extends Controller
{
    /**
     * แสดงฟอร์มสำหรับเพิ่มหนังสือใหม่ และแสดงรายการหนังสือทั้งหมด
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // ดึงหนังสือทั้งหมดจากฐานข้อมูล เรียงตาม created_at ล่าสุด
        $books = Book::latest()->get();

        // ส่งข้อมูล books ไปยัง View
        return view('books.index', compact('books'));
    }

    /**
     * จัดการการบันทึกข้อมูลหนังสือใหม่ลงฐานข้อมูล
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // 1. ตรวจสอบความถูกต้องของข้อมูล (Validation)
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'isbn' => 'nullable|string|unique:books,isbn|max:255', // ISBN ต้องไม่ซ้ำกันในตาราง books
            'publication_year' => 'nullable|integer|min:1000|max:' . (date('Y') + 1), // ปีต้องเป็นตัวเลข และไม่เกินปีปัจจุบัน + 1
        ]);

        // 2. สร้างและบันทึกข้อมูลหนังสือ
        // เราใช้ create() method เพราะเรากำหนด $fillable ใน Model แล้ว
        Book::create($request->all());

        // 3. เปลี่ยนเส้นทางกลับไปยังหน้าเดิม พร้อมข้อความแจ้งเตือน
        return redirect()->route('books.index')->with('success', 'บันทึกหนังสือเรียบร้อยแล้ว!');
    }
}
