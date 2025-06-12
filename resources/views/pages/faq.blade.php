<!-- resources/views/faq.blade.php -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ</title>
    <link rel="stylesheet" href="{{ asset('css/faq.css') }}">
</head>
<style>
    /* public/css/faq.css */

    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    .faq-container {
        max-width: 800px;
        margin: 40px auto;
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    h1.title {
        text-align: center;
        font-size: 32px;
        color: #333;
    }

    .faq-item {
        margin-top: 20px;
    }

    h2.question {
        font-size: 22px;
        color: #333;
        cursor: pointer;
        transition: color 0.3s ease;
    }

    h2.question:hover {
        color: #007bff;
    }

    p.answer {
        font-size: 16px;
        color: #555;
        line-height: 1.6;
        margin-top: 10px;
    }

    a {
        color: #007bff;
        text-decoration: none;
    }

    a:hover {
        text-decoration: underline;
    }
</style>

<body>

    <div class="faq-container">
        <h1 class="title">Frequently Asked Questions (FAQ)</h1>

        <div class="faq-item">
            <h2 class="question">What is AstroGuide?</h2>
            <p class="answer">AstroGuide is an astrology-based service that provides personalized readings based on your birth details, such as zodiac signs, houses, planets, and more.</p>
        </div>

        <div class="faq-item">
            <h2 class="question">How do I book an appointment?</h2>
            <p class="answer">You can book an appointment by selecting an astrologer from our platform, choosing an available slot, and making a payment. Once confirmed, you'll receive a notification with the details.</p>
        </div>

        <div class="faq-item">
            <h2 class="question">How do I contact support?</h2>
            <p class="answer">You can contact our support team by emailing us at <a href="mailto:support@astroguide.com">support@astroguide.com</a>. We are available to assist you with any issues or questions.</p>
        </div>

        <div class="faq-item">
            <h2 class="question">What payment methods do you accept?</h2>
            <p class="answer">We accept a variety of payment methods, including credit cards, debit cards, and digital wallets. Our payment platform is secure and encrypted to protect your transactions.</p>
        </div>

        <div class="faq-item">
            <h2 class="question">Is my data secure on AstroGuide?</h2>
            <p class="answer">Yes, we take the privacy of your data seriously. We use industry-standard encryption to ensure that your personal information is securely stored and processed. For more details, please refer to our <a href="/privacy-policy">Privacy Policy</a>.</p>
        </div>

        <div class="faq-item">
            <h2 class="question">Can I cancel or reschedule my appointment?</h2>
            <p class="answer">Yes, you can cancel or reschedule your appointment. Please check the cancellation policy on our platform or contact our support team for assistance.</p>
        </div>
    </div>

</body>

</html>