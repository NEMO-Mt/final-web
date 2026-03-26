<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAST CAMP - โปรไฟล์</title>
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
                <?php if (isset($_GET['updated'])): ?>
                <div class="w-full bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-2xl mb-6 text-center">
                    แก้ไขโปรไฟล์สำเร็จ
                </div>
                <?php endif; ?>
                
                <div class="w-32 h-32 rounded-full bg-gray-300 border-4 border-white shadow-md mb-4 flex items-center justify-center z-10 relative mt-4 overflow-hidden">
                    <?php if (!empty($user['profile_image'])): ?>
                        <img src="/<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Avatar" class="w-full h-full object-cover bg-white">
                    <?php else: ?>
                        <img src="https://api.dicebear.com/9.x/micah/svg?seed=<?php echo urlencode($user['email']); ?>" alt="Avatar" class="w-full h-full object-cover bg-white">
                    <?php endif; ?>
                </div>

                <h2 class="text-2xl font-bold"><?php echo htmlspecialchars($user['full_name']); ?></h2>
                <p class="text-gray-400 text-sm mb-8"><?php echo htmlspecialchars($user['email']); ?></p>

                <div class="flex gap-4 w-full justify-center mb-8">
                    <div class="bg-secondary rounded-2xl py-4 px-6 flex flex-col items-center w-40 shadow-sm border border-white">
                        <span class="font-bold text-sm mb-1">กิจกรรมที่สร้าง</span>
                        <span class="text-4xl font-bold"><?php echo $createdCount; ?></span>
                    </div>
                    <div class="bg-[#4b85c1] text-white rounded-2xl py-4 px-6 flex flex-col items-center w-40 shadow-sm border border-white">
                        <span class="font-bold text-sm mb-1">การลงทะเบียน</span>
                        <span class="text-4xl font-bold"><?php echo $registrationCount; ?></span>
                    </div>
                </div>

                <div class="w-full px-4 md:px-12 mb-8 text-sm">
                    <h3 class="font-bold text-primary border-b border-primary inline-block mb-4 pb-0.5">ข้อมูลส่วนตัว</h3>
                    <div class="grid grid-cols-2 gap-y-3 gap-x-4">
                        <div class="flex"><span class="w-20 text-gray-500 font-medium">วันเกิด</span> <span class="font-bold"><?php echo date('d/m/Y', strtotime($user['birthday'])); ?></span></div>
                        <div class="flex"><span class="w-16 text-gray-500 font-medium">อายุ</span> <span class="font-bold"><?php echo $age; ?></span></div>
                        <div class="flex"><span class="w-20 text-gray-500 font-medium">เพศ</span> <span class="font-bold"><?php echo $user['gender'] === 'male' ? 'ชาย' : ($user['gender'] === 'female' ? 'หญิง' : 'อื่นๆ'); ?></span></div>
                        <div class="flex"><span class="w-16 text-gray-500 font-medium">อาชีพ</span> <span class="font-bold"><?php echo htmlspecialchars($user['occupation']); ?></span></div>
                        <div class="flex"><span class="w-20 text-gray-500 font-medium">เบอร์โทร</span> <span class="font-bold"><?php echo htmlspecialchars($user['phone']); ?></span></div>
                    </div>
                </div>

                <div class="w-full bg-secondary/50 rounded-[30px] p-6">
                    <h3 class="font-bold mb-4 pl-2">ประวัติการเข้าร่วมกิจกรรม</h3>
                    <div class="space-y-3">
                        <button onclick="toggleSection('upcoming')" class="w-full bg-[#4b85c1] text-white font-medium rounded-xl px-6 py-3 flex justify-between items-center shadow-sm">
                            <span>กำลังจะมาถึง</span>
                            <i id="icon-upcoming" class="fa-solid fa-caret-down"></i>
                        </button>
                        <div id="section-upcoming" class="hidden bg-white rounded-xl p-4">
                            <?php 
                            $upcoming = array_filter($registrations, function($r) { 
                                return strtotime($r['end_date']) >= time() && $r['status'] === 'approved'; 
                            });
                            if (empty($upcoming)): 
                            ?>
                                <p class="text-gray-500 text-center">ไม่มีกิจกรรมที่กำลังจะมาถึง</p>
                            <?php else: 
                                foreach ($upcoming as $reg): 
                            ?>
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span><?php echo htmlspecialchars($reg['title']); ?></span>
                                    <span class="text-sm text-gray-500"><?php echo date('d/m/Y', strtotime($reg['start_date'])); ?></span>
                                </div>
                            <?php endforeach; endif; ?>
                        </div>
                        
                        <button onclick="toggleSection('past')" class="w-full bg-[#4b85c1] text-white font-medium rounded-xl px-6 py-3 flex justify-between items-center shadow-sm opacity-90">
                            <span>ที่ผ่านมา</span>
                            <i id="icon-past" class="fa-solid fa-caret-down"></i>
                        </button>
                        <div id="section-past" class="hidden bg-white rounded-xl p-4">
                            <?php 
                            $past = array_filter($registrations, function($r) { 
                                return strtotime($r['end_date']) < time() || $r['status'] === 'rejected'; 
                            });
                            if (empty($past)): 
                            ?>
                                <p class="text-gray-500 text-center">ไม่มีประวัติการเข้าร่วม</p>
                            <?php else: 
                                foreach ($past as $reg): 
                            ?>
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span><?php echo htmlspecialchars($reg['title']); ?></span>
                                    <span class="text-sm <?php echo $reg['status'] === 'rejected' ? 'text-red-500' : 'text-gray-500'; ?>">
                                        <?php echo $reg['status'] === 'rejected' ? 'ถูกปฏิเสธ' : date('d/m/Y', strtotime($reg['start_date'])); ?>
                                    </span>
                                </div>
                            <?php endforeach; endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-4 w-full justify-center">
                <a href="/edit_profile" class="bg-primary hover:bg-blue-800 text-white font-medium rounded-full py-3 px-8 flex items-center gap-2 transition shadow-md w-48 justify-center">
                    <i class="fa-solid fa-pen text-sm"></i>
                    แก้ไขโปรไฟล์
                </a>
                <a href="/logout" class="bg-[#3e527b] hover:bg-[#2c3d5e] text-white font-medium rounded-full py-3 px-8 flex items-center gap-2 transition shadow-md w-48 justify-center">
                    <i class="fa-solid fa-arrow-right-from-bracket text-sm"></i>
                    ออกจากระบบ
                </a>
            </div>
        </div>
    </main>

    <script>
        function toggleSection(id) {
            const section = document.getElementById('section-' + id);
            const icon = document.getElementById('icon-' + id);
            if (section.classList.contains('hidden')) {
                section.classList.remove('hidden');
                icon.classList.remove('fa-caret-down');
                icon.classList.add('fa-caret-up');
            } else {
                section.classList.add('hidden');
                icon.classList.remove('fa-caret-up');
                icon.classList.add('fa-caret-down');
            }
        }
    </script>
</body>
</html>
