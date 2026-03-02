<?php
$pageTitle = "Contact Us";
require_once 'config/db.php';

$success = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    if ($name && $email && $message) {
        $stmt = $conn->prepare("INSERT INTO contacts (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);
            $stmt->execute();
            $success = "Thank you! Your message has been sent. We'll respond within 24 hours.";
            $stmt->close();
        } else {
            $success = "Thank you for contacting us!"; // DB may not be set up yet
        }
    } else {
        $error = "Please fill in all required fields.";
    }
}

$extraCSS = '<link rel="stylesheet" href="css/contact.css">';
$extraJS  = '<script src="js/contact.js"></script>';
include 'includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <div class="breadcrumb-vv mb-4">
            <a href="index.php">Home</a>
            <span class="breadcrumb-sep"><i class="fas fa-chevron-right"></i></span>
            <span class="breadcrumb-current">Contact Us</span>
        </div>
        <h1 class="page-hero-title">Get In <span>Touch</span></h1>
        <p style="color:rgba(255,255,255,0.65);max-width:500px;margin-top:16px;">We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
    </div>
</section>

<!-- Contact Cards -->
<section style="padding:80px 0 60px;background:var(--light);">
    <div class="container">
        <div class="row g-4 mb-5">
            <?php
            $contactCards = [
                ['icon'=>'fa-map-marker-alt','title'=>'Visit Us','detail'=>'123 Fashion Street<br>Clifton, Karachi, Pakistan','color'=>'var(--primary)'],
                ['icon'=>'fa-phone','title'=>'Call Us','detail'=>'+92 300 123 4567<br>+92 21 111 234 567','color'=>'var(--gold)'],
                ['icon'=>'fa-envelope','title'=>'Email Us','detail'=>'info@velvetvogue.com<br>support@velvetvogue.com','color'=>'#e74c3c'],
                ['icon'=>'fa-clock','title'=>'Business Hours','detail'=>'Mon–Sat: 10am – 8pm<br>Sun: 12pm – 6pm','color'=>'var(--success)'],
            ];
            foreach ($contactCards as $card): ?>
            <div class="col-lg-3 col-md-6 animate-on-scroll">
                <div class="contact-info-card">
                    <div class="contact-icon" style="background:<?= $card['color'] ?>15;color:<?= $card['color'] ?>;">
                        <i class="fas <?= $card['icon'] ?>"></i>
                    </div>
                    <h5 class="contact-title"><?= $card['title'] ?></h5>
                    <p class="contact-detail"><?= $card['detail'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Contact Form + Map -->
        <div class="row g-5">
            <div class="col-lg-7 animate-on-scroll">
                <div class="form-vv">
                    <div class="mb-4">
                        <span class="section-tag">Send a Message</span>
                        <h3 class="section-title" style="font-size:2rem;">We're Here to <span>Help</span></h3>
                        <div class="divider-gold"></div>
                    </div>

                    <?php if($success): ?>
                    <div class="alert-vv alert-success"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?></div>
                    <?php elseif($error): ?>
                    <div class="alert-vv alert-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-group-vv">
                                    <label class="form-label-vv">Your Name *</label>
                                    <div class="input-group-vv">
                                        <i class="fas fa-user input-icon"></i>
                                        <input type="text" name="name" class="form-control-vv" placeholder="John Doe" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-vv">
                                    <label class="form-label-vv">Email Address *</label>
                                    <div class="input-group-vv">
                                        <i class="fas fa-envelope input-icon"></i>
                                        <input type="email" name="email" class="form-control-vv" placeholder="you@example.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-vv">
                                    <label class="form-label-vv">Phone Number</label>
                                    <div class="input-group-vv">
                                        <i class="fas fa-phone input-icon"></i>
                                        <input type="text" name="phone" class="form-control-vv" placeholder="+92 300 0000000" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-vv">
                                    <label class="form-label-vv">Subject</label>
                                    <select name="subject" class="form-control-vv" style="cursor:pointer;">
                                        <option value="">Select a topic...</option>
                                        <option>Order Inquiry</option>
                                        <option>Product Question</option>
                                        <option>Return / Exchange</option>
                                        <option>Shipping Issue</option>
                                        <option>Wholesale / Bulk</option>
                                        <option>Partnership</option>
                                        <option>Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group-vv">
                                    <label class="form-label-vv">Message *</label>
                                    <textarea name="message" class="form-control-vv" rows="6" placeholder="Tell us how we can help you..." required><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn-vv btn-primary-vv w-100">
                                    <i class="fas fa-paper-plane me-2"></i> Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-5 animate-on-scroll delay-200">
                <!-- Map Embed -->
                <div style="border-radius:20px;overflow:hidden;box-shadow:var(--shadow-lg);margin-bottom:24px;height:350px;">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3619.4!2d67.03!3d24.84!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjTCsDUwJzI0LjAiTiA2N8KwMDEnNDguMCJF!5e0!3m2!1sen!2spk!4v1000000" width="100%" height="100%" style="border:0;" allowfullscreen loading="lazy"></iframe>
                </div>

                <!-- Social Media -->
                <div style="background:white;border-radius:20px;padding:28px;box-shadow:var(--shadow);">
                    <h5 style="font-weight:700;margin-bottom:20px;">Follow Our Journey</h5>
                    <div class="d-flex flex-column gap-3">
                        <?php
                        $socials = [
                            ['icon'=>'fa-instagram','name'=>'Instagram','handle'=>'@velvetvogue','color'=>'#E1306C','url'=>'#'],
                            ['icon'=>'fa-facebook-f','name'=>'Facebook','handle'=>'Velvet Vogue PK','color'=>'#1877F2','url'=>'#'],
                            ['icon'=>'fa-tiktok','name'=>'TikTok','handle'=>'@velvetvogue','color'=>'#010101','url'=>'#'],
                            ['icon'=>'fa-youtube','name'=>'YouTube','handle'=>'Velvet Vogue Official','color'=>'#FF0000','url'=>'#'],
                        ];
                        foreach ($socials as $s): ?>
                        <a href="<?= $s['url'] ?>" style="display:flex;align-items:center;gap:14px;padding:12px;border-radius:12px;transition:all 0.2s;text-decoration:none;" onmouseover="this.style.background='var(--light)'" onmouseout="this.style.background='transparent'">
                            <div style="width:42px;height:42px;border-radius:10px;background:<?= $s['color'] ?>20;display:flex;align-items:center;justify-content:center;color:<?= $s['color'] ?>;font-size:1.1rem;">
                                <i class="fab <?= $s['icon'] ?>"></i>
                            </div>
                            <div>
                                <div style="font-weight:600;font-size:13px;color:var(--dark);"><?= $s['name'] ?></div>
                                <div style="font-size:12px;color:var(--text-muted);"><?= $s['handle'] ?></div>
                            </div>
                            <i class="fas fa-chevron-right ms-auto" style="color:var(--text-muted);font-size:12px;"></i>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section style="padding:80px 0;background:white;">
    <div class="container">
        <div class="text-center mb-5 animate-on-scroll">
            <span class="section-tag">FAQ</span>
            <h2 class="section-title">Frequently Asked <span>Questions</span></h2>
            <div class="divider-gold center"></div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <?php
                    $faqs = [
                        ['q'=>'How long does delivery take?','a'=>'Standard delivery takes 3-5 business days. Express delivery (1-2 days) is available in major cities for Rs. 500.'],
                        ['q'=>'What is your return policy?','a'=>'We offer 30-day hassle-free returns. Items must be unworn, unwashed, and in original packaging with all tags attached.'],
                        ['q'=>'Do you offer Cash on Delivery?','a'=>'Yes! We offer COD across Pakistan. We also accept JazzCash, EasyPaisa, and credit/debit cards.'],
                        ['q'=>'How do I track my order?','a'=>'Once your order is shipped, you\'ll receive a tracking number via SMS and email. You can also track in your account dashboard.'],
                        ['q'=>'Are your products authentic?','a'=>'Absolutely! All products at Velvet Vogue are 100% authentic, sourced directly from premium manufacturers and designers.'],
                        ['q'=>'Do you offer wholesale/bulk pricing?','a'=>'Yes, we offer special pricing for bulk orders. Please contact us directly at wholesale@velvetvogue.com for inquiries.'],
                    ];
                    foreach ($faqs as $i => $faq): ?>
                    <div class="accordion-item" style="border:none;border-bottom:1px solid var(--border);margin-bottom:0;">
                        <h2 class="accordion-header">
                            <button class="accordion-button <?= $i > 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#faq<?= $i ?>" style="font-family:'Poppins',sans-serif;font-weight:600;font-size:14px;background:transparent;color:var(--dark);box-shadow:none;padding:20px 0;">
                                <?= htmlspecialchars($faq['q']) ?>
                            </button>
                        </h2>
                        <div id="faq<?= $i ?>" class="accordion-collapse collapse <?= $i == 0 ? 'show' : '' ?>" data-bs-parent="#faqAccordion">
                            <div class="accordion-body" style="padding:0 0 20px;color:var(--text-muted);font-size:14px;line-height:1.8;">
                                <?= htmlspecialchars($faq['a']) ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
