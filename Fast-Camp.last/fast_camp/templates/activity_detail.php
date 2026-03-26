<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAST CAMP - <?php echo htmlspecialchars($activity['title']); ?></title>
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
            <a href="/profile" class="text-gray-500 hover:text-primary transition font-medium border-b-2 border-transparent">โปรไฟล์</a>
        </div>
        <div class="flex items-center gap-3 cursor-pointer" onclick="window.location.href='/profile'">
            <span class="font-medium"><?php echo htmlspecialchars($userName); ?></span>
            <div class="w-10 h-10 rounded-full bg-gray-200 overflow-hidden">
                <img src="https://api.dicebear.com/9.x/micah/svg?seed=<?php echo urlencode($userEmail ?? 'default'); ?>" alt="Avatar" class="w-full h-full object-cover">
            </div>
        </div>
    </nav>

    <main class="flex-grow p-4 md:p-10 flex justify-center items-start">
        <div class="bg-white rounded-[40px] w-full max-w-6xl shadow-2xl overflow-hidden relative border border-gray-100">
            <button onclick="window.location.href='/home'" class="absolute top-4 right-6 text-gray-500 hover:text-gray-800 text-2xl z-10 bg-white/80 rounded-full w-10 h-10 flex items-center justify-center shadow-sm backdrop-blur-sm">
                <i class="fa-solid fa-xmark"></i>
            </button>

            <?php if (!empty($images)): ?>
                <?php $firstImage = $images[0]; ?>
                <div class="w-full h-64 bg-gray-200 flex items-center justify-center overflow-hidden">
                    <img src="/<?php echo htmlspecialchars($firstImage['image_path']); ?>" alt="Cover" class="w-full h-full object-cover">
                </div>
            <?php else: ?>
                <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                    <i class="fa-regular fa-image text-6xl text-white drop-shadow-md"></i>
                </div>
            <?php endif; ?>

            <div class="p-8 md:p-12">
                <?php if ($created): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-2xl mb-6 text-center">
                    สร้างกิจกรรมสำเร็จ
                </div>
                <?php endif; ?>
                <?php if ($registered): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-2xl mb-6 text-center">
                    ลงทะเบียนขอเข้าร่วมกิจกรรมสำเร็จ
                </div>
                <?php endif; ?>
                <?php if (isset($_GET['updated'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-2xl mb-6 text-center">
                    แก้ไขกิจกรรมสำเร็จ
                </div>
                <?php endif; ?>
                <?php if (isset($_GET['approve'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-2xl mb-6 text-center">
                    อนุมัติการเข้าร่วมสำเร็จ
                </div>
                <?php endif; ?>
                <?php if (isset($_GET['reject'])): ?>
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-2xl mb-6 text-center">
                    ปฏิเสธการเข้าร่วมสำเร็จ
                </div>
                <?php endif; ?>
                <?php if (isset($_GET['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-2xl mb-6 text-center">
                    เกิดข้อผิดพลาด กรุณาลองใหม่
                </div>
                <?php endif; ?>

                <h2 class="text-4xl font-bold text-center mb-10"><?php echo htmlspecialchars($activity['title']); ?></h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    <div class="space-y-6">
                        <div class="bg-secondary rounded-full py-2 px-4 flex items-center justify-center gap-3 font-medium">
                            <i class="fa-regular fa-calendar text-lg"></i>
                            <span><?php echo date('d/m/Y', strtotime($activity['start_date'])) . ' - ' . date('d/m/Y', strtotime($activity['end_date'])); ?></span>
                        </div>
                        <div class="flex items-center gap-4 pl-2">
                            <div class="w-16 h-16 bg-secondary rounded-full flex items-center justify-center">
                                <i class="fa-solid fa-user text-2xl text-primary"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">จัดทำโดย</p>
                                <p class="font-bold text-lg"><?php echo htmlspecialchars($activity['owner_name']); ?></p>
                            </div>
                        </div>
                        <div class="pl-2 space-y-3">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-location-dot text-primary w-5 text-center"></i>
                                <span class="text-sm font-medium"><?php echo htmlspecialchars($activity['location']); ?></span>
                            </div>
                            <?php if ($isOwner): ?>
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-users text-primary w-5 text-center"></i>
                                <span class="text-sm font-medium"><?php echo ($stats['approved'] ?? 0) . '/' . ($stats['total'] ?? 0); ?> คน</span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="border-l border-gray-200 pl-8">
                        <h3 class="font-bold text-lg mb-3">รายละเอียด</h3>
                        <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line"><?php echo htmlspecialchars($activity['detail']); ?></p>
                    </div>

                    <div class="border-l border-gray-200 pl-8">
                        <h3 class="font-bold text-lg mb-3">รูปภาพเพิ่มเติม</h3>
                        <div class="grid grid-cols-3 gap-3">
                            <?php foreach ($images as $index => $image): ?>
                                <?php if ($index > 0): ?>
                                <div class="aspect-square rounded-xl overflow-hidden bg-gray-200">
                                    <img src="/<?php echo htmlspecialchars($image['image_path']); ?>" alt="Activity Image" class="w-full h-full object-cover hover:scale-110 transition cursor-pointer" onclick="openModal(this.src)">
                                </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            <?php if (count($images) <= 1): ?>
                            <div class="bg-gray-200 aspect-square rounded-xl flex items-center justify-center col-span-3">
                                <span class="text-gray-400 text-sm">ไม่มีรูปภาพเพิ่มเติม</span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="mt-12 flex justify-center gap-4">
                    <?php if ($isOwner): ?>
                        <a href="/edit?id=<?php echo $activity['activity_id']; ?>" class="bg-secondary text-primary font-bold py-3 px-10 rounded-2xl flex items-center gap-2 hover:bg-blue-200 transition shadow-sm text-lg">
                            <i class="fa-solid fa-pen"></i>
                            แก้ไขกิจกรรม
                        </a>
                        <a href="/stats?activity_id=<?php echo $activity['activity_id']; ?>" class="bg-primary text-white font-bold py-3 px-10 rounded-2xl flex items-center gap-2 hover:bg-blue-800 transition shadow-sm text-lg">
                            <i class="fa-solid fa-chart-bar"></i>
                            ดูสถิติ
                        </a>
                        <a href="/checkin?activity_id=<?php echo $activity['activity_id']; ?>" class="bg-green-600 text-white font-bold py-3 px-10 rounded-2xl flex items-center gap-2 hover:bg-green-700 transition shadow-sm text-lg">
                            <i class="fa-solid fa-check"></i>
                            เช็คชื่อ
                        </a>
                        <form method="POST" action="/delete" onsubmit="return confirm('ต้องการลบกิจกรรมนี้?');" class="inline">
                            <input type="hidden" name="id" value="<?php echo $activity['activity_id']; ?>">
                            <button type="submit" class="bg-red-500 text-white font-bold py-3 px-10 rounded-2xl flex items-center gap-2 hover:bg-red-600 transition shadow-sm text-lg">
                                <i class="fa-solid fa-trash"></i>
                                ลบ
                            </button>
                        </form>
                    <?php else: ?>
                        <?php if ($registration): ?>
                            <?php if ($registration['status'] === 'pending'): ?>
                                <div class="bg-yellow-100 text-yellow-800 font-bold py-3 px-10 rounded-2xl flex items-center gap-2 text-lg">
                                    <i class="fa-solid fa-clock"></i>
                                    รอการอนุมัติ
                                </div>
                            <?php elseif ($registration['status'] === 'approved'): ?>
                                <?php if ($registration['is_checkin']): ?>
                                    <div class="bg-green-100 text-green-800 font-bold py-3 px-10 rounded-2xl flex items-center gap-2 text-lg">
                                        <i class="fa-solid fa-check-circle"></i>
                                        เข้าร่วมแล้ว
                                    </div>
                                <?php else: ?>
                                    <button onclick="showOTP()" class="bg-secondary text-primary font-bold py-3 px-10 rounded-2xl flex items-center gap-2 hover:bg-blue-200 transition shadow-sm text-lg">
                                        <i class="fa-solid fa-qrcode"></i>
                                        แสดงรหัส OTP
                                    </button>
                                    <div id="otp-display" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50">
                                        <div class="bg-white rounded-[30px] p-8 text-center">
                                            <h3 class="text-2xl font-bold mb-4">รหัส OTP ของคุณ</h3>
                                            <div id="otp-code" class="text-4xl font-bold text-primary mb-4">------</div>
                                            <p id="otp-timer" class="text-gray-500 mb-4">หมดอายุใน 30 นาที</p>
                                            <button onclick="generateOTP()" class="bg-primary text-white px-6 py-2 rounded-full hover:bg-blue-800 transition">
                                                สร้างรหัสใหม่
                                            </button>
                                            <button onclick="hideOTP()" class="block mx-auto mt-4 text-gray-500 hover:text-gray-700">
                                                ปิด
                                            </button>
                                        </div>
                                    </div>
                                    <script>
                                        function showOTP() {
                                            document.getElementById('otp-display').classList.remove('hidden');
                                            generateOTP();
                                        }
                                        function hideOTP() {
                                            document.getElementById('otp-display').classList.add('hidden');
                                        }
                                        function generateOTP() {
                                            fetch('/generate_otp?activity_id=<?php echo $activity['activity_id']; ?>')
                                                .then(r => r.json())
                                                .then(data => {
                                                    if (data.success) {
                                                        document.getElementById('otp-code').textContent = data.otp;
                                                        updateTimer(data.expires_in);
                                                    }
                                                });
                                        }
                                        function updateTimer(seconds) {
                                            const mins = Math.floor(seconds / 60);
                                            document.getElementById('otp-timer').textContent = 'หมดอายุใน ' + mins + ' นาที';
                                        }
                                    </script>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="bg-red-100 text-red-800 font-bold py-3 px-10 rounded-2xl flex items-center gap-2 text-lg">
                                    <i class="fa-solid fa-times-circle"></i>
                                    ถูกปฏิเสธ
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <form method="POST" action="/register_activity">
                                <input type="hidden" name="activity_id" value="<?php echo $activity['activity_id']; ?>">
                                <button type="submit" class="bg-secondary text-primary font-bold py-3 px-10 rounded-2xl flex items-center gap-2 hover:bg-blue-200 transition shadow-sm text-lg">
                                    <i class="fa-solid fa-plus"></i>
                                    เข้าร่วมกิจกรรม
                                </button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <?php if ($isOwner && !empty($registrations)): ?>
                <div class="mt-12">
                    <h3 class="font-bold text-xl mb-6">รายชื่อผู้ลงทะเบียน</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-secondary">
                                <tr>
                                    <th class="py-3 px-4 text-left rounded-tl-xl">ชื่อ</th>
                                    <th class="py-3 px-4 text-left">อีเมล</th>
                                    <th class="py-3 px-4 text-left">เพศ</th>
                                    <th class="py-3 px-4 text-left">อาชีพ</th>
                                    <th class="py-3 px-4 text-left">สถานะ</th>
                                    <th class="py-3 px-4 text-left">เช็คชื่อ</th>
                                    <th class="py-3 px-4 text-left rounded-tr-xl">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($registrations as $reg): ?>
                                <tr class="border-b border-gray-100">
                                    <td class="py-3 px-4"><?php echo htmlspecialchars($reg['full_name']); ?></td>
                                    <td class="py-3 px-4"><?php echo htmlspecialchars($reg['email']); ?></td>
                                    <td class="py-3 px-4"><?php echo $reg['gender'] === 'male' ? 'ชาย' : ($reg['gender'] === 'female' ? 'หญิง' : 'อื่นๆ'); ?></td>
                                    <td class="py-3 px-4"><?php echo htmlspecialchars($reg['occupation']); ?></td>
                                    <td class="py-3 px-4">
                                        <?php if ($reg['status'] === 'pending'): ?>
                                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs">รออนุมัติ</span>
                                        <?php elseif ($reg['status'] === 'approved'): ?>
                                            <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">อนุมัติ</span>
                                        <?php else: ?>
                                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs">ปฏิเสธ</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-3 px-4">
                                        <?php echo $reg['is_checkin'] ? '<i class="fa-solid fa-check text-green-500"></i>' : '<i class="fa-solid fa-times text-gray-300"></i>'; ?>
                                    </td>
                                    <td class="py-3 px-4">
                                        <?php if ($reg['status'] === 'pending'): ?>
                                        <form method="POST" action="/approve" class="inline">
                                            <input type="hidden" name="reg_id" value="<?php echo $reg['reg_id']; ?>">
                                            <button type="submit" name="action" value="approve" class="bg-green-500 text-white px-3 py-1 rounded-lg text-xs hover:bg-green-600 transition mr-1">อนุมัติ</button>
                                            <button type="submit" name="action" value="reject" class="bg-red-500 text-white px-3 py-1 rounded-lg text-xs hover:bg-red-600 transition">ปฏิเสธ</button>
                                        </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
