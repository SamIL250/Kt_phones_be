<?php
include './src/layout/layout.php';

$message_status = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $subject = filter_var($_POST['subject'], FILTER_SANITIZE_STRING);
    $message = filter_var($_POST['message'], FILTER_SANITIZE_STRING);
    $created_at = date('Y-m-d H:i:s');

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $message_status = 'error';
    } else {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $subject, $message, $created_at);

        if ($stmt->execute()) {
            $message_status = 'success';
        } else {
            $message_status = 'error';
        }
        $stmt->close();
    }
}
?>

<div class="bg-gray-50 font-sans">
    <div class="container max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <!-- Page Header -->
        <div class="text-center mb-12 observe-card">
            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-800">Contact Us</h1>
            <p class="text-lg text-gray-500 mt-2">We're here to help. Reach out to us anytime.</p>
            <div class="mt-4 mx-auto w-24 h-1 bg-blue-600 rounded"></div>
        </div>

        <div class="bg-white p-8 sm:p-10 rounded-lg shadow-lg observe-card">
            <div class="grid md:grid-cols-2 gap-12">
                <!-- Contact Form -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Send us a Message</h2>
                    <?php if ($message_status == 'success') : ?>
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md" role="alert">
                            <p class="font-bold">Success!</p>
                            <p>Your message has been sent successfully. We will get back to you shortly.</p>
                        </div>
                    <?php elseif ($message_status == 'error') : ?>
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">
                            <p class="font-bold">Error</p>
                            <p>There was a problem sending your message. Please fill all fields and try again.</p>
                        </div>
                    <?php endif; ?>

                    <form action="contact" method="POST" class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" name="name" id="name" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <input type="email" name="email" id="email" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                            <input type="text" name="subject" id="subject" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                            <textarea name="message" id="message" rows="4" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                        <div>
                            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-blue-700 transition-all shadow-md">
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Contact Info & Map -->
                <div class="space-y-8">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Contact Information</h2>
                        <div class="space-y-4 text-gray-600">
                            <p class="flex items-center"><i class="bi bi-geo-alt-fill text-blue-500 mr-4 text-xl"></i>123 Tech Avenue, Silicon Valley, CA 94000</p>
                            <p class="flex items-center"><i class="bi bi-telephone-fill text-blue-500 mr-4 text-xl"></i>(123) 456-7890</p>
                            <p class="flex items-center"><i class="bi bi-envelope-fill text-blue-500 mr-4 text-xl"></i><a href="mailto:support@ktphones.com" class="hover:underline">support@ktphones.com</a></p>
                        </div>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Business Hours</h2>
                        <div class="space-y-2 text-gray-600">
                            <p><strong>Monday - Friday:</strong> 9:00 AM - 6:00 PM</p>
                            <p><strong>Saturday:</strong> 10:00 AM - 4:00 PM</p>
                            <p><strong>Sunday:</strong> Closed</p>
                        </div>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Our Location</h2>
                        <div class="aspect-w-16 aspect-h-9 rounded-lg overflow-hidden shadow-md">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3172.339763539352!2d-122.084!3d37.422!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8085808add8f0233%3A0x72d1c682702dc5d2!2sGoogleplex!5e0!3m2!1sen!2sus!4v1616161616161" width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include './src/components/footer.php'; ?>
</body>

</html>