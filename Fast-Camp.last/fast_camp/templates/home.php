<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAST CAMP - หน้าหลัก</title>
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
        <!-- logo -->
        <div class="flex items-center gap-3 cursor-pointer" onclick="window.location.href='/home'">
            <div class="bg-primary text-white w-10 h-10 rounded-lg flex items-center justify-center text-xl shadow-md">
                <i class="fa-solid fa-campground"></i>
            </div>
            <h1 class="text-2xl font-bold tracking-wide">FAST CAMP</h1>
        </div>
        
        <div class="hidden md:flex bg-white rounded-full shadow-sm px-6 py-2 gap-8 items-center">
            <a href="/home" class="text-primary font-medium border-b-2 border-primary transition">หน้าหลัก</a>
            <a href="/my_activities" class="text-gray-500 hover:text-primary transition font-medium border-b-2 border-transparent">กิจกรรมของฉัน</a>
            <a href="/create" class="text-gray-500 hover:text-primary transition font-medium border-b-2 border-transparent">สร้างกิจกรรม</a>
            <a href="/profile" class="text-gray-500 hover:text-primary transition font-medium border-b-2 border-transparent">โปรไฟล์</a>
        </div>
        <div class="flex items-center gap-3 cursor-pointer" onclick="window.location.href='/profile'">
            <span class="font-medium"><?php echo htmlspecialchars($userName); ?></span>
            <div class="w-10 h-10 rounded-full bg-gray-200 overflow-hidden">
                <img src="https://api.dicebear.com/9.x/micah/svg?seed=<?php echo urlencode($_SESSION['user_email'] ?? 'default'); ?>" alt="Avatar" class="w-full h-full object-cover">
            </div>
        </div>
    </nav>

    <main class="flex-grow p-8 max-w-7xl mx-auto w-full">
        <div class="bg-surface rounded-full py-3 px-6 flex flex-col md:flex-row justify-between items-center gap-4 mb-10 shadow-sm mx-auto max-w-5xl">
            <form method="GET" action="/home" class="flex flex-col items-center gap-4 w-full">
                <div class="relative w-full">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 transform -translate-y-1/2 text-primary"></i>
                    <input type="text" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="ค้นหากิจกรรม" class="w-full pl-12 pr-4 py-2 rounded-full bg-white outline-none focus:ring-2 focus:ring-primary/30 shadow-inner">
                </div>
                <div class="flex flex-wrap items-center justify-center gap-4 text-sm font-medium w-full">
                    <div class="flex items-center gap-2">
                        <label>วันที่เริ่ม:</label>
                        <div class="bg-white rounded-full px-4 py-1.5 flex items-center shadow-inner">
                            <input type="date" name="start_date" value="<?php echo htmlspecialchars($startDate); ?>" class="outline-none text-gray-600 bg-transparent text-sm" id="start_date_input">
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <label>วันที่สิ้นสุด:</label>
                        <div class="bg-white rounded-full px-4 py-1.5 flex items-center shadow-inner">
                            <input type="date" name="end_date" value="<?php echo htmlspecialchars($endDate); ?>" class="outline-none text-gray-600 bg-transparent text-sm" id="end_date_input">
                        </div>
                    </div>
                    <button type="submit" class="bg-primary text-white px-4 py-1.5 rounded-full hover:bg-blue-800 transition">
                        <i class="fa-solid fa-search"></i>
                    </button>
                </div>
            </form>
        </div>

        <?php if (isset($_GET['deleted'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-2xl mb-6 text-center">
            ลบกิจกรรมสำเร็จ
        </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($activities as $activity): 
                $actImages = getActivityImages($activity['activity_id']);
            ?>
            <div class="bg-white rounded-[30px] p-4 pb-5 shadow-sm hover:shadow-md transition flex flex-col items-center text-center border border-gray-50 h-full">
                <?php if (!empty($actImages)): ?>
                <div class="w-full h-40 bg-secondary rounded-[20px] mb-4 overflow-hidden">
                    <img src="<?php echo htmlspecialchars($actImages[0]['image_path']); ?>" alt="<?php echo htmlspecialchars($activity['title']); ?>" class="w-full h-full object-cover">
                </div>
                <?php else: ?>
                <div class="w-full h-40 bg-secondary rounded-[20px] mb-4 flex items-center justify-center">
                    <i class="fa-regular fa-image text-4xl text-white/50"></i>
                </div>
                <?php endif; ?>
                <div class="self-start bg-secondary text-primary text-xs font-bold py-1 px-3 rounded-full mb-3 ml-2">
                    <?php echo date('d/m/Y', strtotime($activity['start_date'])); ?>
                </div>
                <h3 class="font-bold text-lg mb-2"><?php echo htmlspecialchars($activity['title']); ?></h3>
                <p class="text-sm text-gray-500 mb-4 px-2 leading-tight flex-grow line-clamp-2"><?php echo htmlspecialchars($activity['detail']); ?></p>
                <div class="flex items-center gap-2 text-xs font-medium text-gray-600 mb-5">
                    <i class="fa-solid fa-location-dot text-accent"></i>
                    <span><?php echo htmlspecialchars($activity['location']); ?></span>
                </div>
                <a href="/activity/<?php echo $activity['activity_id']; ?>" class="w-[90%] bg-secondary hover:bg-blue-200 text-primary font-bold py-2.5 rounded-2xl transition text-sm text-center">
                    รายละเอียด
                </a>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($activities)): ?>
        <div class="text-center py-12">
            <i class="fa-solid fa-search text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-500">ไม่พบกิจกรรม</p>
        </div>
        <?php endif; ?>
    </main>

    <script>
        // Format date inputs to display dd/mm/yyyy
        function formatDateInput(inputId) {
            const input = document.getElementById(inputId);
            if (!input) return;
            
            // Create a custom date input with dd/mm/yyyy display
            const wrapper = document.createElement('div');
            wrapper.style.position = 'relative';
            input.parentNode.insertBefore(wrapper, input);
            wrapper.appendChild(input);
            
            // Create display input
            const displayInput = document.createElement('input');
            displayInput.type = 'text';
            displayInput.placeholder = 'dd/mm/yyyy';
            displayInput.className = input.className;
            displayInput.style.cssText = input.style.cssText;
            displayInput.readOnly = true;
            
            // Hide original input
            input.style.position = 'absolute';
            input.style.opacity = '0';
            input.style.pointerEvents = 'none';
            
            // Insert display input before original
            input.parentNode.insertBefore(displayInput, input);
            
            function updateDisplay() {
                if (input.value) {
                    const date = new Date(input.value);
                    const day = String(date.getDate()).padStart(2, '0');
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const year = date.getFullYear();
                    displayInput.value = `${day}/${month}/${year}`;
                } else {
                    displayInput.value = '';
                }
            }
            
            // Update display when value changes
            input.addEventListener('change', updateDisplay);
            
            // Show date picker when display input is clicked
            displayInput.addEventListener('click', () => {
                input.focus();
                input.showPicker?.();
            });
            
            // Initial display update
            updateDisplay();
        }
        
        // Apply to both date inputs
        document.addEventListener('DOMContentLoaded', function() {
            formatDateInput('start_date_input');
            formatDateInput('end_date_input');
        });
    </script>
</body>
</html>
