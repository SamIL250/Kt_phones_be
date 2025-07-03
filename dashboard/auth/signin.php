<?php
session_start();
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KT-Phones | Auth</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Permanent+Marker&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Sriracha&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <style>
        * {
            font-family: "Poppins";
        }
    </style>
</head>

<body class="bg-blue-50 min-vh-100 flex justify-center items-center p-5 lg:p-20">
    <?php
    if (isset($_SESSION['notification'])) {
    ?>
        <script>
            if (window.Notyf) {
                (window.notyfInstance = window.notyfInstance || new Notyf({ duration: 3000, position: { x: 'right', y: 'top' } }));
                notyfInstance.error("⚠️<?= $_SESSION['notification'] ?>");
            }
        </script>
    <?php
    }
    unset($_SESSION['notification']);
    ?>
    <script>
        // Toastify({
        //     text: "Hello ffro",
        //     className: "info",
        //     close: true,
        //     stopOnFocus: true,
        //     backgroundColor: "linear-gradient(to right, #0F172A, #1E293B)", // Dark blue gradient
        //     className: "custom-toast",
        //     stopOnFocus: true, // Prevents dismissing on hover
        //     style: {
        //         borderRadius: "8px",
        //         padding: "12px 16px",
        //         color: "#fff",
        //         fontSize: "14px",
        //         fontWeight: "500",
        //         boxShadow: "0px 4px 10px rgba(0, 0, 0, 0.15)",
        //     }
        // }).showToast();

        // Toastify({
        //         text: "⚠️Hello error",
        //         className: "info",
        //         close: true,
        //         stopOnFocus: true,
        //         backgroundColor: "linear-gradient(to right, #F97316, #EA580C)", // Dark blue gradient
        //         className: "warning-toast ",
        //         stopOnFocus: true, // Prevents dismissing on hover
        //         style: {
        //             borderRadius: "8px",
        //             padding: "12px 16px",
        //             color: "#fff",
        //             fontSize: "14px",
        //             fontWeight: "500",
        //             boxShadow: "0px 4px 10px rgba(0, 0, 0, 0.15)",
        //             borderLeft: "5px solid #C2410C" // A left border for emphasis
        //         }
        //     }).showToast();
    </script>
    <div class="sm:w-[100%] lg:w-[70%] bg-white rounded-md border-1 border-gray-300 shadow-sm grid grid-cols-1  lg:grid-cols-2">
        <div class="border-r-1 border-gray-200 p-10">
            <div class="flex justify-center">
                <div class="flex items-center gap-2">
                    <img src="../src/assets/img/icons/logo.png" class="w-[40px]" alt="" srcset="">
                    <p>KT-Phones</p>
                </div>
            </div>
            <div class="py-20 text-center">
                <p class="text-gray-600 font-bold">Welcome to KT-Phones admin portal</p>
            </div>
            <div class="text-center text-gray-600 text-sm">
                <p>If you don'y have an account, kindly get in touch with CEO of KT-Phones ltd for assistance.</p>
            </div>
            <div class="my-10 text-center">
                <button class="text-gray-600 font-bold py-1 px-4 border-1 border-gray-300 shadow-sm  rounded-md cursor-pointer">Contact</button>
            </div>
        </div>
        <div class="py-10 px-10 lg:px-20">
            <div class="pb-3">
                <p class="text-lg text-gray-60">Sign In</p>
            </div>
            <div><small class="text-gray-600">Sign In with your account yo continue</small></div>
            <form action="../src/services/auth/signin.php" method="POST" class="py-10 grid gap-4">
                <div>
                    <input type="email" name="email" class="border-1 border-gray-300 py-2 px-4 w-[100%] rounded-md outline-none focus:border-blue-400 text-sm" placeholder="Enter email" id="">
                </div>
                <div>
                    <input type="password" name="password" class="border-1 border-gray-300 py-2 px-4 w-[100%] rounded-md outline-none focus:border-blue-400 text-sm" placeholder="Enter password" id="">
                </div>

                <div>
                    <p class="text-[12px]">Can't access your account? <a href="" class="border-b border-gray-300 text-blue-400">Reset your password.</a></p>
                </div>
                <div class="mt-5">
                    <button class="bg-blue-400 text-white font-bold py-1 px-4 border-1 border-gray-300 shadow-sm  rounded-md cursor-pointer">Sign In</button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>