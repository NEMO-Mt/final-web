<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAST CAMP - แก้ไขโปรไฟล์</title>
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
            <a href="/create" class="text-gray-500 hover:text-primary transition font-medium border-b-2 border-transparent">สร้างกิจกรรม</a>
            <a href="/profile" class="text-primary font-medium border-b-2 border-primary transition">โปรไฟล์</a>
        </div>
        <div class="flex items-center gap-3 cursor-pointer" onclick="window.location.href='/profile'">
            <span class="font-medium"><?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></span>
            <div class="w-10 h-10 rounded-full bg-gray-200 overflow-hidden">
                <img src="https://api.dicebear.com/9.x/micah/svg?seed=<?php echo urlencode($_SESSION['user_email'] ?? 'default'); ?>" alt="Avatar" class="w-full h-full object-cover">
            </div>
        </div>
    </nav>

    <main class="flex-grow p-4 md:p-10 flex justify-center items-start">
        <div class="w-full max-w-2xl flex flex-col items-center gap-6">
            <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 w-full p-8 flex flex-col items-center relative overflow-hidden">
                <h2 class="text-2xl font-bold mb-8">แก้ไขโปรไฟล์</h2>
                
                <!-- Profile Avatar (Dicebear only - no upload) -->
                <div class="flex flex-col items-center mb-8">
                    <div class="w-32 h-32 rounded-full bg-gray-200 border-4 border-white shadow-md mb-4 overflow-hidden relative">
                        <img src="https://api.dicebear.com/9.x/micah/svg?seed=<?php echo urlencode($user['email']); ?>" alt="Avatar" class="w-full h-full object-cover bg-white">
                    </div>
                
                </div>
                
                <?php if ($error): ?>
                <div class="w-full bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-2xl mb-6 text-center">
                    <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="/edit_profile" class="w-full space-y-6">

                    <!-- Full Name -->
                    <div>
                        <label class="block font-bold mb-2 pl-2">ชื่อ-นามสกุล <span class="text-red-500">*</span></label>
                        <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required class="w-full bg-surface rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary/50 text-primary transition">
                    </div>

                    <!-- Birthday -->
                    <div>
                        <label class="block font-bold mb-2 pl-2">วันเกิด <span class="text-red-500">*</span></label>
                        <input type="date" name="birthday" value="<?php echo $user['birthday']; ?>" required class="w-full bg-surface rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary/50 text-primary transition">
                    </div>

                    <!-- Gender -->
                    <div>
                        <label class="block font-bold mb-2 pl-2">เพศ <span class="text-red-500">*</span></label>
                        <select name="gender" required class="w-full bg-surface rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary/50 text-primary transition">
                            <option value="male" <?php echo $user['gender'] === 'male' ? 'selected' : ''; ?>>ชาย</option>
                            <option value="female" <?php echo $user['gender'] === 'female' ? 'selected' : ''; ?>>หญิง</option>
                            <option value="other" <?php echo $user['gender'] === 'other' ? 'selected' : ''; ?>>อื่นๆ</option>
                        </select>
                    </div>

                    <!-- Occupation -->
                    <div>
                        <label class="block font-bold mb-2 pl-2">อาชีพ <span class="text-red-500">*</span></label>
                        <input type="text" name="occupation" value="<?php echo htmlspecialchars($user['occupation']); ?>" required class="w-full bg-surface rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary/50 text-primary transition">
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block font-bold mb-2 pl-2">เบอร์โทรศัพท์ <span class="text-red-500">*</span></label>
                        <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required class="w-full bg-surface rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary/50 text-primary transition">
                    </div>

                    <!-- Email (readonly) -->
                    <div>
                        <label class="block font-bold mb-2 pl-2">อีเมล</label>
                        <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled class="w-full bg-gray-100 rounded-2xl px-4 py-3 text-gray-500 cursor-not-allowed">
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-4 pt-4">
                        <button type="submit" class="flex-1 bg-primary hover:bg-blue-800 text-white font-bold py-3 rounded-2xl transition shadow-md">
                            บันทึกการแก้ไข
                        </button>
                        <a href="/profile" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 rounded-2xl transition text-center">
                            ยกเลิก
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
