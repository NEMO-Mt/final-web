<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAST CAMP - แก้ไขกิจกรรม</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Kanit', 'sans-serif'] },
                    colors: {
                        primary: '#1c3671',
                        secondary: '#c8defa',
                        surface: '#e3efff',
                        bg_main: '#f2f6fc',
                        accent: '#e93b81'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-bg_main font-sans text-primary min-h-screen flex flex-col">
    <nav class="bg-bg_main py-4 px-8 flex justify-between items-center sticky top-0 z-50">
        <div class="flex items-center gap-3 cursor-pointer" onclick="window.location.href='/home'">
            <div class="bg-primary text-white w-10 h-10 rounded-lg flex items-center justify-center text-xl shadow-md">
                <i class="fa-solid fa-campground"></i>
            </div>
            <h1 class="text-2xl font-bold tracking-wide">FAST CAMP</h1>
        </div>
        <div class="hidden md:flex bg-white rounded-full shadow-sm px-6 py-2 gap-8 items-center">
            <a href="/home" class="text-gray-500 hover:text-primary transition font-medium border-b-2 border-transparent">หน้าหลัก</a>
            <a href="/my_activities" class="text-gray-500 hover:text-primary transition font-medium border-b-2 border-transparent">กิจกรรมของฉัน</a>
            <a href="/create" class="text-primary font-medium border-b-2 border-primary transition">สร้างกิจกรรม</a>
            <a href="/profile" class="text-gray-500 hover:text-primary transition font-medium border-b-2 border-transparent">โปรไฟล์</a>
        </div>
        <div class="flex items-center gap-3 cursor-pointer" onclick="window.location.href='/profile'">
            <span class="font-medium"><?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></span>
            <div class="w-10 h-10 rounded-full bg-gray-200 overflow-hidden">
                <img src="https://api.dicebear.com/9.x/micah/svg?seed=<?php echo urlencode($_SESSION['user_email'] ?? 'default'); ?>" alt="Avatar" class="w-full h-full object-cover">
            </div>
        </div>
    </nav>

    <main class="flex-grow p-4 md:p-8 flex justify-center pb-20">
        <div class="bg-white rounded-[30px] p-8 md:p-12 w-full max-w-3xl shadow-sm border border-gray-100">
            <h2 class="text-center font-bold text-2xl mb-8">แก้ไขกิจกรรม</h2>
            
            <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-2xl mb-6">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="/edit/<?php echo $activity['activity_id']; ?>" enctype="multipart/form-data" class="space-y-6">
                <div>
                    <label class="block font-bold mb-3 text-lg">รูปภาพกิจกรรม</label>
                    
                    <?php if (!empty($images)): ?>
                    <div class="grid grid-cols-4 gap-3 mb-4">
                        <?php foreach ($images as $image): ?>
                        <div class="aspect-square rounded-xl overflow-hidden bg-gray-100 relative group">
                            <img src="<?php echo htmlspecialchars($image['image_path']); ?>" alt="Activity Image" class="w-full h-full object-cover">
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    
                    <div class="border-2 border-dashed border-secondary bg-surface/50 rounded-2xl p-6 flex flex-col items-center justify-center cursor-pointer hover:bg-surface transition" onclick="document.getElementById('images').click()">
                        <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-primary text-xl shadow-sm mb-2">
                            <i class="fa-solid fa-plus"></i>
                        </div>
                        <span class="text-sm text-primary mb-2">เพิ่มรูปภาพใหม่</span>
                        <span class="text-xs text-gray-500">รองรับ JPG, PNG, GIF, WebP (สูงสุด 5MB)</span>
                        <input type="file" id="images" name="images[]" multiple accept="image/*" class="hidden" onchange="previewImages(this)">
                    </div>
                    <div id="preview" class="grid grid-cols-4 gap-3 mt-4"></div>
                </div>

                <div>
                    <label class="block font-bold mb-2">ชื่อกิจกรรม <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($activity['title']); ?>" required class="w-full border border-gray-200 rounded-xl px-4 py-3 outline-none focus:border-primary focus:ring-1 focus:ring-primary transition text-sm placeholder-gray-400">
                </div>

                <div>
                    <label class="block font-bold mb-2">รายละเอียดกิจกรรม <span class="text-red-500">*</span></label>
                    <textarea name="detail" rows="4" required class="w-full border border-gray-200 rounded-xl px-4 py-3 outline-none focus:border-primary focus:ring-1 focus:ring-primary transition text-sm placeholder-gray-400 resize-none"><?php echo htmlspecialchars($activity['detail']); ?></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-bold mb-2">วันที่เริ่มกิจกรรม <span class="text-red-500">*</span></label>
                        <input type="date" name="start_date" value="<?php echo date('Y-m-d', strtotime($activity['start_date'])); ?>" required class="w-full border border-gray-200 rounded-xl px-4 py-3 outline-none focus:border-primary transition text-sm text-gray-500">
                    </div>
                    <div>
                        <label class="block font-bold mb-2">วันที่สิ้นสุดกิจกรรม <span class="text-red-500">*</span></label>
                        <input type="date" name="end_date" value="<?php echo date('Y-m-d', strtotime($activity['end_date'])); ?>" required class="w-full border border-gray-200 rounded-xl px-4 py-3 outline-none focus:border-primary transition text-sm text-gray-500">
                    </div>
                </div>

                <div>
                    <label class="block font-bold mb-2">สถานที่ <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i class="fa-solid fa-location-dot absolute left-4 top-1/2 transform -translate-y-1/2 text-accent"></i>
                        <input type="text" name="location" value="<?php echo htmlspecialchars($activity['location']); ?>" required class="w-full border border-gray-200 rounded-xl pl-10 pr-4 py-3 outline-none focus:border-primary transition text-sm placeholder-gray-400">
                    </div>
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="submit" class="flex-1 bg-primary hover:bg-blue-800 text-white font-bold py-3 rounded-2xl transition shadow-md">
                        บันทึกการแก้ไข
                    </button>
                    <a href="/activity/<?php echo $activity['activity_id']; ?>" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 rounded-2xl transition text-center">
                        ยกเลิก
                    </a>
                </div>
            </form>
        </div>
    </main>

    <script>
        function previewImages(input) {
            const preview = document.getElementById('preview');
            preview.innerHTML = '';
            
            if (input.files) {
                Array.from(input.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'aspect-square rounded-xl overflow-hidden bg-gray-100';
                        div.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                        preview.appendChild(div);
                    }
                    reader.readAsDataURL(file);
                });
            }
        }
    </script>
</body>
</html>
