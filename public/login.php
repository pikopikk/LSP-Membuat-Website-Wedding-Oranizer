<?php
session_start();
include __DIR__ . '/../src/php/config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM tb_users_opik WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Username atau password salah.';
            }
        } catch (PDOException $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Ahuy WO</title>
    <link rel="stylesheet" href="./css/styles.css">
       <link rel="stylesheet" href="./css/styles.css">
   <link href="../node_modules/remixicon/fonts/remixicon.css" rel="stylesheet">
   <link rel="icon" href="img" type="image/png">
</head>
<body>
<?php include __DIR__ . '/../src/php/includes/header.php' ?>

    <section class="ezy__signup11 light py-14 md:py-24 bg-[#A8BBA3]">
        <div class="container px-4 mx-auto">
            <div class="grid grid-cols-12 lg:gap-24 h-full">
                <div class="col-span-12 lg:col-span-6">
                    <div
                        class="bg-center bg-no-repeat bg-cover w-full min-h-[150px] rounded-[25px] hidden lg:block h-full"
                        style="background-image: url(./img/login.png)"
                    ></div>
                </div>
                <div class="col-span-12 lg:col-span-5 py-14 lg:py-24">
                    <div class="h-full max-w-xl bg-white shadow-xl  rounded-xl p-6 lg:p-14">
                        <div class="w-full max-w-xl mx-auto">
                            <h2 class="text-stone-800 text-2xl font-bold mb-3">Welcome to Ahuy WO</h2>
                            <?php if ($error): ?>
                                <p class="text-red-500 mb-4"><?php echo htmlspecialchars($error); ?></p>
                            <?php endif; ?>
                            <form method="POST">
                                <div class="mb-4 relative">
                                    <i class="ri-user-line absolute left-3 top-3 text-gray-400"></i>
                                    <input
                                        type="text"
                                        name="username"
                                        class="w-full bg-[#F7F4EA] min-h-[48px] pl-10 pr-4 p-2 rounded-lg outline-none border border-transparent focus:border-[#F7F4EA]"
                                        id="username"
                                        placeholder="Enter Username"
                                        value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                                    />
                                </div>
                                <div class="mb-4 relative">
                                    <i class="ri-lock-line absolute left-3 top-3 text-gray-400"></i>
                                    <input
                                        type="password"
                                        name="password"
                                        class="w-full bg-[#F7F4EA] min-h-[48px] pl-10 pr-4 p-2 rounded-lg outline-none border border-transparent focus:border-[#F7F4EA]"
                                        id="password"
                                        placeholder="Enter Password"
                                    />
                                </div>
                                <!-- <div class="mb-4 flex items-center">
                                    <input type="checkbox" class="mr-2" id="remember-me" name="remember-me" checked />
                                    <label class="font-normal text-gray-700 dark:text-gray-300" for="remember-me">Remember me</label>
                                </div> -->
                                <button type="submit" class="bg-[#B87C4C] text-white py-3 px-6 rounded w-full hover:cursor-pointer hover:bg-[#ad6020]">Log In</button>
                                <!-- <a href="#" class="block text-center hover:text-blue-600 py-2 px-4 rounded-lg w-full">Forget your password?</a> -->
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include __DIR__ . '/../src/php/includes/footer.php' ?>
</body>
</html>