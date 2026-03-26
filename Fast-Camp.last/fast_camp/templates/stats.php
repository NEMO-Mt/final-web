<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAST CAMP - สถิติกิจกรรม</title>
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

    <main class="flex-grow p-8 max-w-6xl mx-auto w-full">
        <div class="mb-6">
            <a href="/activity?id=<?php echo $activity['activity_id']; ?>" class="text-primary hover:underline"><i class="fa-solid fa-arrow-left mr-2"></i> กลับไปหน้ากิจกรรม</a>
        </div>

        <h2 class="text-3xl font-bold mb-2">สถิติกิจกรรม</h2>
        <h3 class="text-xl text-gray-600 mb-8"><?php echo htmlspecialchars($activity['title']); ?></h3>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-50 text-center">
                <div class="text-4xl font-bold text-primary mb-2"><?php echo $stats['total']; ?></div>
                <div class="text-gray-500">ผู้ลงทะเบียนทั้งหมด</div>
            </div>
            <div class="bg-yellow-50 rounded-[20px] p-6 shadow-sm border border-yellow-100 text-center">
                <div class="text-4xl font-bold text-yellow-600 mb-2"><?php echo $stats['pending']; ?></div>
                <div class="text-gray-500">รออนุมัติ</div>
            </div>
            <div class="bg-green-50 rounded-[20px] p-6 shadow-sm border border-green-100 text-center">
                <div class="text-4xl font-bold text-green-600 mb-2"><?php echo $stats['approved']; ?></div>
                <div class="text-gray-500">อนุมัติแล้ว</div>
            </div>
            <div class="bg-blue-50 rounded-[20px] p-6 shadow-sm border border-blue-100 text-center">
                <div class="text-4xl font-bold text-blue-600 mb-2"><?php echo $stats['checked_in']; ?></div>
                <div class="text-gray-500">เช็คชื่อแล้ว</div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-50">
                <h4 class="font-bold text-lg mb-4"><i class="fa-solid fa-venus-mars mr-2 text-accent"></i>สถิติตามเพศ</h4>
                <div class="space-y-3">
                    <?php 
                    $maleCount = $stats['gender']['male'] ?? 0;
                    $femaleCount = $stats['gender']['female'] ?? 0;
                    $otherCount = $stats['gender']['other'] ?? 0;
                    $totalGender = $maleCount + $femaleCount + $otherCount;
                    ?>
                    <div class="flex items-center gap-4">
                        <span class="w-16">ชาย</span>
                        <div class="flex-1 bg-gray-100 rounded-full h-6">
                            <div class="bg-blue-500 h-6 rounded-full flex items-center justify-center text-white text-xs" style="width: <?php echo $totalGender > 0 ? ($maleCount / $totalGender * 100) : 0; ?>%">
                                <?php echo $maleCount; ?>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="w-16">หญิง</span>
                        <div class="flex-1 bg-gray-100 rounded-full h-6">
                            <div class="bg-pink-500 h-6 rounded-full flex items-center justify-center text-white text-xs" style="width: <?php echo $totalGender > 0 ? ($femaleCount / $totalGender * 100) : 0; ?>%">
                                <?php echo $femaleCount; ?>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="w-16">อื่นๆ</span>
                        <div class="flex-1 bg-gray-100 rounded-full h-6">
                            <div class="bg-gray-500 h-6 rounded-full flex items-center justify-center text-white text-xs" style="width: <?php echo $totalGender > 0 ? ($otherCount / $totalGender * 100) : 0; ?>%">
                                <?php echo $otherCount; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-50">
                <h4 class="font-bold text-lg mb-4"><i class="fa-solid fa-cake-candles mr-2 text-accent"></i>สถิติตามช่วงอายุ</h4>
                <div class="space-y-3">
                    <?php 
                    $ageGroups = $stats['age_groups'] ?? [];
                    $ageLabels = [
                        'under18' => 'ต่ำกว่า 18',
                        '18-25' => '18-25',
                        '26-35' => '26-35',
                        '36-50' => '36-50',
                        'over50' => '50 ขึ้นไป'
                    ];
                    $totalAge = array_sum($ageGroups);
                    foreach ($ageLabels as $key => $label):
                        $count = $ageGroups[$key] ?? 0;
                    ?>
                    <div class="flex items-center gap-4">
                        <span class="w-20 text-sm"><?php echo $label; ?></span>
                        <div class="flex-1 bg-gray-100 rounded-full h-6">
                            <div class="bg-primary h-6 rounded-full flex items-center justify-center text-white text-xs" style="width: <?php echo $totalAge > 0 ? ($count / $totalAge * 100) : 0; ?>%">
                                <?php echo $count; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-[20px] p-6 shadow-sm border border-gray-50">
            <h4 class="font-bold text-lg mb-4">รายชื่อผู้ลงทะเบียน</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-secondary">
                        <tr>
                            <th class="py-3 px-4 text-left rounded-tl-xl">ชื่อ</th>
                            <th class="py-3 px-4 text-left">เพศ</th>
                            <th class="py-3 px-4 text-left">อายุ</th>
                            <th class="py-3 px-4 text-left">อาชีพ</th>
                            <th class="py-3 px-4 text-left">สถานะ</th>
                            <th class="py-3 px-4 text-left rounded-tr-xl">เช็คชื่อ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($registrations as $reg): 
                            $age = $reg['birthday'] ? floor((time() - strtotime($reg['birthday'])) / 31557600) : '-';
                        ?>
                        <tr class="border-b border-gray-100">
                            <td class="py-3 px-4"><?php echo htmlspecialchars($reg['full_name']); ?></td>
                            <td class="py-3 px-4"><?php echo $reg['gender'] === 'male' ? 'ชาย' : ($reg['gender'] === 'female' ? 'หญิง' : 'อื่นๆ'); ?></td>
                            <td class="py-3 px-4"><?php echo $age; ?></td>
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
                                <?php echo $reg['is_checkin'] ? '<span class="text-green-500"><i class="fa-solid fa-check"></i> แล้ว</span>' : '<span class="text-gray-400">-</span>'; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>
