<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

    body {
        font-family: 'Inter', sans-serif;
    }

    .gradient-bg {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .lock-animation {
        animation: shake 0.5s ease-in-out;
    }

    @keyframes shake {

        0%,
        100% {
            transform: translateX(0);
        }

        25% {
            transform: translateX(-5px);
        }

        75% {
            transform: translateX(5px);
        }
    }

    .floating {
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-10px);
        }
    }

    .pulse-glow {
        animation: pulse-glow 2s ease-in-out infinite alternate;
    }

    @keyframes pulse-glow {
        from {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
        }

        to {
            box-shadow: 0 0 30px rgba(59, 130, 246, 0.6);
        }
    }

    .slide-in {
        animation: slideIn 0.8s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>


<div class="min-h-screen flex items-start justify-center px-4 pt-20 pb-8">
    <div class="glass-card rounded-3xl p-8 md:p-12 max-w-4xl w-full slide-in">
        <div class="flex flex-col md:flex-row items-center gap-8 md:gap-12">

            <!-- Left Column -->
            <div class="md:w-1/3 text-center">
                <!-- Lock Icon -->
                <div class="mb-8 floating">
                    <div class="w-32 h-32 mx-auto bg-red-500 rounded-full flex items-center justify-center lock-animation pulse-glow">
                        <svg class="w-16 h-16 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                </div>
                <!-- Error Code -->
                <div class="text-center">
                    <h1 class="observe-card text-7xl font-bold text-gray-600 mb-2">401</h1>
                    <h2 class="observe-card text-2xl font-semibold text-gray-600">Unauthorized</h2>
                </div>
            </div>

            <!-- Right Column -->
            <div class="md:w-2/3">
                <!-- Error Details -->
                <div class="bg-white bg-opacity-10 rounded-xl p-6 mb-6 text-left">
                    <h3 class="observe-card text-lg font-semibold text-gray-600 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        What happened?
                    </h3>
                    <p class="observe-card text-gray-600 text-sm leading-relaxed">
                        You don't have permission to access this resource. This usually means you need to sign in with valid credentials or your session has expired.
                    </p>
                </div>

                <!-- Solutions -->
                <div class="bg-white bg-opacity-10 rounded-xl p-6 mb-6 text-left">
                    <h3 class="observe-card text-lg font-semibold text-gray-600 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        How to fix this:
                    </h3>
                    <ul class="text-gray-500 text-sm space-y-2">
                        <li class="observe-card flex items-start">
                            <span class="text-blue-300 mr-2">•</span>
                            Sign in with your account credentials
                        </li>
                        <li class="observe-card flex items-start">
                            <span class="text-blue-300 mr-2">•</span>
                            Contact support if you believe this is an error
                        </li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-4">
                    <button onclick="window.location.replace('signin')" class="observe-card w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-3 px-6 rounded-full transition-all duration-300 transform">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                        </svg>
                        Sign In
                    </button>

                    <button onclick="window.history.back()" class="observe-card w-full border-2 border-white border-opacity-30 text-gray-600 font-semibold py-3 px-6 rounded-xl transition-all duration-300 hover:bg-white hover:bg-opacity-10 hover:border-opacity-50">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Go Back
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Set current timestamp
    document.getElementById('timestamp').textContent = new Date().toLocaleString();

    // Add some interactivity
    document.addEventListener('DOMContentLoaded', function() {
        // Trigger lock animation on page load
        setTimeout(() => {
            document.querySelector('.lock-animation').style.animation = 'shake 0.5s ease-in-out';
        }, 500);
    });
</script>