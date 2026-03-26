<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAST CAMP - สมัครสมาชิก</title>
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
<body class="bg-bg_main font-sans text-primary min-h-screen flex items-center justify-center p-4">
    <div class="bg-white rounded-[40px] shadow-xl p-10 w-full max-w-lg flex flex-col items-center">
        <div class="mb-4 text-5xl">
            <img src="https://cdn-icons-png.flaticon.com/512/4712/4712035.png" alt="Robot Icon" class="w-20 h-20 object-contain opacity-80 mix-blend-multiply filter grayscale drop-shadow-sm">
        </div>
        <h1 class="text-2xl font-bold text-primary mb-6 tracking-wider">สมัครสมาชิก FAST CAMP</h1>

        <?php if ($error): ?>
        <div class="w-full bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-2xl mb-4">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="/register" class="w-full space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-primary font-medium mb-1 pl-2 text-sm">อีเมล</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>" required class="w-full bg-surface rounded-2xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-primary/50 text-primary transition text-sm">
                </div>
                <div>
                    <label class="block text-primary font-medium mb-1 pl-2 text-sm">ชื่อ-นามสกุล</label>
                    <input type="text" name="full_name" value="<?php echo htmlspecialchars($formData['full_name'] ?? ''); ?>" required class="w-full bg-surface rounded-2xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-primary/50 text-primary transition text-sm">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-primary font-medium mb-1 pl-2 text-sm">รหัสผ่าน</label>
                    <input type="password" name="password" required class="w-full bg-surface rounded-2xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-primary/50 text-primary transition text-sm">
                </div>
                <div>
                    <label class="block text-primary font-medium mb-1 pl-2 text-sm">ยืนยันรหัสผ่าน</label>
                    <input type="password" name="confirm_password" required class="w-full bg-surface rounded-2xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-primary/50 text-primary transition text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-primary font-medium mb-1 pl-2 text-sm">วันเกิด</label>
                    <input type="date" name="birthday" value="<?php echo htmlspecialchars($formData['birthday'] ?? ''); ?>" required class="w-full bg-surface rounded-2xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-primary/50 text-primary transition text-sm">
                </div>
                <div>
                    <label class="block text-primary font-medium mb-1 pl-2 text-sm">เพศ</label>
                    <select name="gender" required class="w-full bg-surface rounded-2xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-primary/50 text-primary transition text-sm">
                        <option value="">เลือกเพศ</option>
                        <option value="male" <?php echo ($formData['gender'] ?? '') === 'male' ? 'selected' : ''; ?>>ชาย</option>
                        <option value="female" <?php echo ($formData['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>หญิง</option>
                        <option value="other" <?php echo ($formData['gender'] ?? '') === 'other' ? 'selected' : ''; ?>>อื่นๆ</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-primary font-medium mb-1 pl-2 text-sm">อาชีพ</label>
                    <input type="text" name="occupation" value="<?php echo htmlspecialchars($formData['occupation'] ?? ''); ?>" required class="w-full bg-surface rounded-2xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-primary/50 text-primary transition text-sm">
                </div>
                <div>
                    <label class="block text-primary font-medium mb-1 pl-2 text-sm">เบอร์โทรศัพท์</label>
                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($formData['phone'] ?? ''); ?>" required class="w-full bg-surface rounded-2xl px-4 py-2.5 outline-none focus:ring-2 focus:ring-primary/50 text-primary transition text-sm">
                </div>
            </div>

            <button type="submit" class="w-full bg-primary hover:bg-blue-800 text-white font-medium rounded-2xl py-3 mt-4 transition shadow-md hover:shadow-lg">
                สมัครสมาชิก
            </button>
            <p class="text-center mt-4 font-medium text-sm">
                มีบัญชีอยู่แล้ว? <a href="/login" class="text-primary underline">เข้าสู่ระบบ</a>
            </p>
        </form>
    </div>
</body>
</html>
