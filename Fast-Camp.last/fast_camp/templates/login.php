<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAST CAMP - เข้าสู่ระบบ</title>
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
    <div class="bg-white rounded-[40px] shadow-xl p-10 w-full max-w-md flex flex-col items-center">
        <div class="mb-4 text-5xl">
            <img src="https://cdn-icons-png.flaticon.com/512/4712/4712035.png" alt="Robot Icon" class="w-24 h-24 object-contain opacity-80 mix-blend-multiply filter grayscale drop-shadow-sm">
        </div>
        <h1 class="text-3xl font-bold text-primary mb-10 tracking-wider">FAST CAMP</h1>

        <?php if ($error): ?>
        <div class="w-full bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-2xl mb-4">
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <?php if (isset($_GET['registered'])): ?>
        <div class="w-full bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-2xl mb-4">
            สมัครสมาชิกสำเร็จ กรุณาเข้าสู่ระบบ
        </div>
        <?php endif; ?>

        <form method="POST" action="/login" class="w-full space-y-6">
            <div>
                <label class="block text-primary font-medium mb-2 pl-2">e-mail</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required class="w-full bg-surface rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary/50 text-primary transition">
            </div>
            <div>
                <label class="block text-primary font-medium mb-2 pl-2">password</label>
                <input type="password" name="password" required class="w-full bg-surface rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-primary/50 text-primary transition">
            </div>
            <button type="submit" class="w-full bg-primary hover:bg-blue-800 text-white font-medium rounded-2xl py-3 mt-4 transition shadow-md hover:shadow-lg">
                เข้าสู่ระบบ
            </button>
            <p class="text-center mt-6 font-medium text-sm">
                ยังไม่มีบัญชี? <a href="/register" class="text-primary underline">ลงทะเบียน</a>
            </p>
        </form>
    </div>
</body>
</html>
