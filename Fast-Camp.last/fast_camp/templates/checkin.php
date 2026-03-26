<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAST CAMP - เช็คชื่อ</title>
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
            <span class="font-medium"><?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?></span>
            <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center">
                <i class="fa-solid fa-user"></i>
            </div>
        </div>
    </nav>

    <main class="flex-grow p-8 max-w-4xl mx-auto w-full">
        <div class="mb-6">
            <a href="/activity?id=<?php echo $activityId; ?>" class="text-primary hover:underline"><i class="fa-solid fa-arrow-left mr-2"></i> กลับไปหน้ากิจกรรม</a>
        </div>

        <h2 class="text-3xl font-bold mb-8 text-center">เช็คชื่อเข้าร่วมกิจกรรม</h2>

        <?php if ($error): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-2xl mb-6 text-center">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-2xl mb-6 text-center">
            <?php echo htmlspecialchars($success); ?>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-[30px] p-8 shadow-sm border border-gray-100 mb-8">
            <form method="POST" action="/checkin?activity_id=<?php echo $activityId; ?>" class="space-y-6">
                <div>
                    <label class="block font-bold mb-3 text-lg text-center">ป้อนรหัส OTP จากผู้เข้าร่วม</label>
                    <input type="text" name="otp" maxlength="6" placeholder="000000" class="w-full max-w-xs mx-auto block text-center text-3xl font-bold tracking-[0.5em] bg-surface rounded-2xl px-4 py-4 outline-none focus:ring-2 focus:ring-primary/50 text-primary transition" required>
                </div>
                <button type="submit" class="w-full max-w-xs mx-auto block bg-primary hover:bg-blue-800 text-white font-bold py-4 rounded-2xl transition shadow-md text-lg">
                    <i class="fa-solid fa-check mr-2"></i>ยืนยันการเช็คชื่อ
                </button>
            </form>
        </div>

        <?php if (!empty($pendingCheckIns)): ?>
        <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100">
            <h3 class="font-bold text-lg mb-4">รายชื่อผู้รอเช็คชื่อ</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-secondary">
                        <tr>
                            <th class="py-3 px-4 text-left rounded-tl-xl">ชื่อ</th>
                            <th class="py-3 px-4 text-left">เพศ</th>
                            <th class="py-3 px-4 text-left">อาชีพ</th>
                            <th class="py-3 px-4 text-left rounded-tr-xl">เช็คชื่อ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingCheckIns as $reg): ?>
                        <tr class="border-b border-gray-100">
                            <td class="py-3 px-4"><?php echo htmlspecialchars($reg['full_name']); ?></td>
                            <td class="py-3 px-4"><?php echo $reg['gender'] === 'male' ? 'ชาย' : ($reg['gender'] === 'female' ? 'หญิง' : 'อื่นๆ'); ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($reg['occupation']); ?></td>
                            <td class="py-3 px-4">
                                <form method="POST" action="/checkin?activity_id=<?php echo $activityId; ?>" class="flex items-center gap-2">
                                    <input type="hidden" name="reg_id" value="<?php echo $reg['reg_id']; ?>">
                                    <input type="text" name="otp" maxlength="6" placeholder="OTP" class="w-20 text-center bg-surface rounded-lg px-2 py-1 text-sm outline-none focus:ring-2 focus:ring-primary/50" required>
                                    <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded-lg text-xs hover:bg-green-600 transition">
                                        <i class="fa-solid fa-check"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php else: ?>
        <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-100 text-center">
            <p class="text-gray-500">ไม่มีผู้รอเช็คชื่อ</p>
        </div>
        <?php endif; ?>
    </main>
</body>
</html>
