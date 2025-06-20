{{-- resources/views/temple_display.blade.php --}}
<!DOCTYPE html>
<html lang="en" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>วัดไทยในต่างแดน</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Custom CSS สำหรับรูปภาพ Item: เพื่อ Crop ภาพขนาด 300x300 โดยไม่บิดเบี้ยว */
        .imagewatthai2 {
            width: 300px;
            height: 300px;
            object-fit: cover;
            /* **สำคัญ:** ทำให้รูปภาพถูก Crop ให้พอดีกับขนาดที่กำหนด */
            object-position: center;
            /* จัดตำแหน่งกึ่งกลางของรูปภาพ */
        }

        /* Custom CSS สำหรับ Container พื้นหลัง: รูปภาพพื้นหลังที่ไม่บิดเบี้ยว */
        #temple {
            background-image: url('https://source.unsplash.com/random/1920x1080/?thailand-temple');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        /* ซ่อน Scrollbar สำหรับ Carousel (ปรับปรุง UX) */
        .carousel-container::-webkit-scrollbar {
            display: none;
        }

        .carousel-container {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* กำหนดความกว้างของแต่ละ Item ใน Carousel ตามจำนวนคอลัมภ์ที่เลือก */
        .carousel-item-width-2 {
            flex: 0 0 50%;
        }

        .carousel-item-width-3 {
            flex: 0 0 33.333333%;
        }

        .carousel-item-width-4 {
            flex: 0 0 25%;
        }

        .carousel-item-fixed {
            flex-shrink: 0;
        }
    </style>
</head>

<body class="font-sans antialiased">

    {{-- Container หลักที่มีภาพพื้นหลัง --}}
    <div id="temple" class="templeall w-full">
        <div class="card w-full max-w-6xl bg-base-100 shadow-xl p-6 md:p-8 lg:p-10">
            <div class="card-body">
                <h2 class="card-title text-3xl md:text-4xl text-center mb-6 text-primary">วัดไทยในต่างแดน</h2>

                {{-- Controls สำหรับปรับจำนวนคอลัมภ์และ Auto Play --}}
                <div x-data="carousel()" x-init="init()" class="relative"> {{-- <--- x-data ย้ายมาอยู่ตรงนี้ เพื่อให้ controls เข้าถึง state ได้ --}}
                    <div class="flex flex-wrap justify-center items-center gap-4 mb-8">
                        {{-- Toggle สำหรับ Auto Play --}}
                        <div class="form-control">
                            <label class="label cursor-pointer gap-2">
                                <span class="label-text">Auto Play</span>
                                <input type="checkbox" class="toggle toggle-primary" x-model="autoplayEnabled" />
                            </label>
                        </div>

                        {{-- Input สำหรับปรับความเร็ว Auto Play --}

                        {{-- Dropdown สำหรับเลือกจำนวนคอลัมภ์ --}}
                        <div class="dropdown dropdown-hover">
                            <div tabindex="0" role="button" class="btn btn-neutral">คอลัมภ์: <span
                                    x-text="columns"></span></div>
                            <ul tabindex="0" class="dropdown-content flex z-[1] menu p-2 bg-base-100  w-52">
                                <li class="p-2 " style="background-color: rgb(127, 214, 255); border-radius: 50px"><a
                                        @click="columns = 2">2 คอลัมภ์</a></li>
                                <li class="p-2 " style="background-color: rgb(255, 127, 165); border-radius: 50px"><a
                                        @click="columns = 3">3 คอลัมภ์</a></li>
                                <li class="p-2 " style="background-color: rgb(127, 255, 148); border-radius: 50px"><a
                                        @click="columns = 4">4 คอลัมภ์</a></li>
                            </ul>
                        </div>
                    </div>

                    {{-- Debug/สถานะ Alpine.js (เพื่อยืนยันว่า Alpine.js ทำงาน) --}}
                    <div class="bg-red-800 text-sm text-gray-500 text-center mb-4">
                        สถานะ Alpine.js:
                        <span class="font-bold">Auto Play: <span
                                x-text="autoplayEnabled ? 'เปิด' : 'ปิด'"></span></span> |
                        <span class="font-bold">คอลัมภ์: <span x-text="columns"></span></span> |
                        <span class="font-bold">หน้าปัจจุบัน: <span x-text="currentPage + 1"></span></span>
                    </div>
                    <div class="flex justify-center w-full py-2 gap-2 mt-4">
                        <template x-for="(_, index) in Math.ceil(items.length / columns)" :key="index">
                            <a href="#" @click.prevent="goToPage(index)" class="btn btn-xs shadow-md"
                                style="border-radius: 50px; padding: 0 12px;"
                                :class="{ 'btn-primary': currentPage === index }">
                                <span x-text="index + 1"></span>
                            </a>
                        </template>
                    </div>

                    {{-- พื้นที่แสดงผล Item (Carousel) --}}
                    <div class="flex">
                        <div class="flex items-center">
                            {{-- ปุ่มเลื่อนก่อนหน้า --}}
                            <button @click="prev()" style="font-size: 25px; padding: 3px; border-radius: 20px;"  class="btn btn-circle btn-primary shadow-lg"><</button>
                        </div>
                        <div x-ref="carousel"
                            class="carousel carousel-center w-full p-4 space-x-4 bg-neutral rounded-box carousel-container flex overflow-x-auto overflow-x-scroll">
                            {{-- วนลูปสร้าง Item 10 ชิ้น --}}
                            @for ($i = 1; $i <= 10; $i++)
                                <div id="watthai"
                                    class="wat carousel-item carousel-item-fixed rounded-box bg-base-200 shadow-md p-4"
                                    :class="{
                                        'carousel-item-width-2': columns === 2,
                                        'carousel-item-width-3': columns === 3,
                                        'carousel-item-width-4': columns === 4
                                    }">
                                    <div class="flex flex-col items-center">
                                        <img id="imagewatthai" class="imagewatthai2 rounded-lg mb-4"
                                            src="https://picsum.photos/300/300?random={{ $i }}"
                                            alt="วัดที่ {{ $i }}">
                                        <h3 class="text-lg font-semibold text-center mb-1">วัดไทยในต่างแดนที่
                                            {{ $i }}</h3>
                                        <p class="text-sm text-gray-500 text-center">ประเทศ: XXXXX, รัฐ: YYYYY</p>
                                        <p class="text-xs text-gray-400 mt-2">รายละเอียดสั้นๆ ของวัด</p>
                                    </div>
                                </div>
                            @endfor
                        </div>

                        {{-- ปุ่มควบคุมการเลื่อน (ซ้าย-ขวา) --}}
                        <div class="flex items-center">
                            {{-- ปุ่มเลื่อนก่อนหน้า --}}
                            <button @click="next()" style="font-size: 25px; padding: 3px; border-radius: 20px;"  class="btn btn-circle btn-primary shadow-lg">></button>
                        </div>
                    </div>
                </div>

                {{-- ตัวบ่งชี้หน้า (Indicators) --}}
                {{-- <div class="flex justify-center w-full py-2 gap-2 mt-4" style="background-color: aquamarine">
                    <template x-for="(_, index) in Math.ceil(items.length / columns)" :key="index">
                        <a href="#" @click.prevent="goToPage(index)" class="btn btn-xs"
                            style="background-color: tomato" :class="{ 'btn-primary': currentPage === index }">
                            <span x-text="index + 1">เหำเ</span>
                        </a>
                    </template>
                </div> --}}


            </div>
        </div>
    </div>

    {{-- Alpine.js Script Logic --}}
    <script>
        function carousel() {
            return {
                columns: 3, // จำนวนคอลัมภ์เริ่มต้น
                autoplayEnabled: true, // เปิด Auto Play เริ่มต้น
                autoplayInterval: 3000, // หน่วงเวลา Auto Play (3 วินาที)
                autoplayTimer: null,
                items: [], // เก็บ Element ของแต่ละ Carousel Item
                currentPage: 0, // Page Index ปัจจุบัน (สำหรับ Indicators)

                init() {
                    this.$nextTick(() => {
                        this.items = Array.from(this.$refs.carousel.children);
                        this.startAutoplay();

                        // ตรวจสอบการเปลี่ยนแปลงของ autoplayEnabled เพื่อเริ่ม/หยุด Auto Play
                        this.$watch('autoplayEnabled', (value) => {
                            if (value) {
                                this.startAutoplay();
                            } else {
                                this.stopAutoplay();
                            }
                        });

                        // ตรวจสอบการเปลี่ยนแปลงของ autoplayInterval เพื่อรีเซ็ต Auto Play Timer
                        this.$watch('autoplayInterval', (value) => {
                            // ตรวจสอบให้แน่ใจว่าเป็นตัวเลขและอยู่ในช่วงที่เหมาะสม
                            if (typeof value === 'number' && value >= 500 && value <= 10000) {
                                if (this.autoplayEnabled) {
                                    this.startAutoplay(); // รีเซ็ต Timer ด้วยค่า Interval ใหม่
                                }
                            }
                        });

                        // เมื่อจำนวนคอลัมภ์เปลี่ยน ให้กลับไปที่หน้าแรก
                        this.$watch('columns', () => {
                            this.goToPage(0);
                        });
                    });
                },

                // เริ่มการทำงาน Auto Play
                startAutoplay() {
                    this.stopAutoplay();
                    if (this.autoplayEnabled) {
                        this.autoplayTimer = setInterval(() => {
                            this.nextPage();
                        }, this.autoplayInterval);
                    }
                },

                // หยุดการทำงาน Auto Play
                stopAutoplay() {
                    if (this.autoplayTimer) {
                        clearInterval(this.autoplayTimer);
                        this.autoplayTimer = null;
                    }
                },

                // เลื่อนไปหน้าถัดไป
                nextPage() {
                    const totalPages = Math.ceil(this.items.length / this.columns);
                    let nextPage = this.currentPage + 1;
                    if (nextPage >= totalPages) {
                        nextPage = 0;
                    }
                    this.goToPage(nextPage);
                },

                // เลื่อนไปหน้าก่อนหน้า
                prevPage() {
                    const totalPages = Math.ceil(this.items.length / this.columns);
                    let prevPage = this.currentPage - 1;
                    if (prevPage < 0) {
                        prevPage = totalPages - 1;
                    }
                    this.goToPage(prevPage);
                },

                // เลื่อนไปยังหน้าที่ระบุ
                goToPage(pageIndex) {
                    const totalPages = Math.ceil(this.items.length / this.columns);
                    if (pageIndex < 0 || pageIndex >= totalPages) return;

                    this.currentPage = pageIndex;
                    const firstItemOnPage = this.items[pageIndex * this.columns];
                    if (firstItemOnPage) {
                        this.$refs.carousel.scrollTo({
                            left: firstItemOnPage.offsetLeft - this.$refs.carousel.offsetLeft,
                            behavior: 'smooth'
                        });
                    }
                    if (this.autoplayEnabled) {
                        this.startAutoplay(); // รีเซ็ต Timer หลังจากเลื่อนเอง
                    }
                },

                // Proxy methods สำหรับปุ่มลูกศร (เรียกใช้ nextPage/prevPage)
                next() {
                    this.nextPage();
                    if (this.autoplayEnabled) {
                        this.startAutoplay();
                    }
                },
                prev() {
                    this.prevPage();
                    if (this.autoplayEnabled) {
                        this.startAutoplay();
                    }
                }
            };
        }
    </script>
</body>

</html>
