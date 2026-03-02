<?php
$pageTitle = "About Us";
require_once 'config/db.php';
$extraCSS = '<link rel="stylesheet" href="css/about.css">';
$extraJS  = '<script src="js/about.js"></script>';
include 'includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero">
    <div class="container">
        <div class="breadcrumb-vv mb-4">
            <a href="index.php">Home</a>
            <span class="breadcrumb-sep"><i class="fas fa-chevron-right"></i></span>
            <span class="breadcrumb-current">About Us</span>
        </div>
        <h1 class="page-hero-title">Our <span>Story</span></h1>
        <p style="color:rgba(255,255,255,0.65);max-width:540px;margin-top:16px;font-size:1.05rem;">
            Born from a love of fashion, Velvet Vogue is where elegance meets modernity — crafted for those who dare to be different.
        </p>
    </div>
</section>

<!-- Our Story -->
<section class="section-pd" style="background:var(--light);">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 animate-on-scroll">
                <div style="position:relative;border-radius:24px;overflow:hidden;">
                    <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=700&h=550&fit=crop" alt="Our Story" style="width:100%;border-radius:24px;box-shadow:0 20px 60px rgba(108,52,131,0.2);">
                    <div style="position:absolute;bottom:28px;right:28px;background:white;border-radius:16px;padding:20px 24px;box-shadow:0 10px 40px rgba(0,0,0,0.15);">
                        <div style="font-family:'Playfair Display',serif;font-size:2.5rem;font-weight:700;color:var(--primary);" class="counter-num" data-target="8" data-suffix="+">0+</div>
                        <div style="font-size:13px;color:var(--text-muted);">Years of Excellence</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 animate-on-scroll delay-200">
                <span class="section-tag">About Us</span>
                <h2 class="section-title">We Are <span>Velvet Vogue</span></h2>
                <div class="divider-gold"></div>
                <p style="color:var(--text-muted);margin-bottom:20px;line-height:1.9;">
                    Founded in 2016, Velvet Vogue began as a small boutique with one simple belief: <strong>every woman and man deserves to feel extraordinary every day</strong>.
                </p>
                <p style="color:var(--text-muted);margin-bottom:28px;line-height:1.9;">
                    Today, we've grown into a leading online fashion destination serving thousands of style-conscious individuals across Pakistan. Our curated collections span everything from everyday essentials to show-stopping statement pieces.
                </p>
                <div class="row g-3 mb-32">
                    <?php
                    $vals = [
                        ['icon'=>'fa-heart','title'=>'Passion Driven','color'=>'#e74c3c'],
                        ['icon'=>'fa-leaf','title'=>'Sustainable','color'=>'#27ae60'],
                        ['icon'=>'fa-award','title'=>'Premium Quality','color'=>'#D4AF37'],
                        ['icon'=>'fa-users','title'=>'Customer First','color'=>'#6C3483'],
                    ];
                    foreach ($vals as $v): ?>
                    <div class="col-6">
                        <div style="display:flex;align-items:center;gap:12px;padding:16px;background:white;border-radius:12px;box-shadow:0 4px 15px rgba(0,0,0,0.05);">
                            <div style="width:42px;height:42px;border-radius:10px;background:<?= $v['color'] ?>15;display:flex;align-items:center;justify-content:center;color:<?= $v['color'] ?>;font-size:1rem;flex-shrink:0;">
                                <i class="fas <?= $v['icon'] ?>"></i>
                            </div>
                            <span style="font-weight:600;font-size:13px;color:var(--dark);"><?= $v['title'] ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <a href="products.php" class="btn-vv btn-primary-vv mt-4">
                    <i class="fas fa-shopping-bag me-2"></i> Explore Collection
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section style="padding:80px 0;background:linear-gradient(135deg,var(--dark),var(--dark-2));">
    <div class="container">
        <div class="row g-4 text-center animate-on-scroll">
            <?php
            $stats = [
                ['num'=>5000,'suffix'=>'+','label'=>'Happy Customers'],
                ['num'=>1200,'suffix'=>'+','label'=>'Products Available'],
                ['num'=>8,'suffix'=>' Years','label'=>'In Business'],
                ['num'=>98,'suffix'=>'%','label'=>'Satisfaction Rate'],
            ];
            foreach ($stats as $s): ?>
            <div class="col-6 col-lg-3">
                <div style="padding:32px 20px;">
                    <div class="counter-num" data-target="<?= $s['num'] ?>" data-suffix="<?= $s['suffix'] ?>" style="font-family:'Playfair Display',serif;font-size:3rem;font-weight:700;color:var(--gold);">0</div>
                    <div style="color:rgba(255,255,255,0.6);margin-top:8px;font-size:14px;"><?= $s['label'] ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Mission & Vision -->
<section class="section-pd" style="background:white;">
    <div class="container">
        <div class="text-center mb-5 animate-on-scroll">
            <span class="section-tag">Our Purpose</span>
            <h2 class="section-title">Mission & <span>Vision</span></h2>
            <div class="divider-gold center"></div>
        </div>
        <div class="row g-4">
            <div class="col-md-4 animate-on-scroll">
                <div style="text-align:center;padding:40px 28px;border-radius:20px;background:var(--light);transition:all 0.3s;border-bottom:4px solid var(--primary);">
                    <div style="width:80px;height:80px;background:linear-gradient(135deg,var(--primary),var(--primary-light));border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;font-size:2rem;color:white;">🎯</div>
                    <h4 style="font-family:'Playfair Display',serif;margin-bottom:16px;">Our Mission</h4>
                    <p style="color:var(--text-muted);font-size:14px;line-height:1.8;">To make premium fashion accessible to everyone in Pakistan, delivering quality, style, and confidence with every purchase.</p>
                </div>
            </div>
            <div class="col-md-4 animate-on-scroll delay-200">
                <div style="text-align:center;padding:40px 28px;border-radius:20px;background:var(--light);transition:all 0.3s;border-bottom:4px solid var(--gold);">
                    <div style="width:80px;height:80px;background:linear-gradient(135deg,var(--gold),#f0c040);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;font-size:2rem;">👁️</div>
                    <h4 style="font-family:'Playfair Display',serif;margin-bottom:16px;">Our Vision</h4>
                    <p style="color:var(--text-muted);font-size:14px;line-height:1.8;">To become Pakistan's most trusted online fashion destination, recognized globally for our unique blend of traditional craftsmanship and modern design.</p>
                </div>
            </div>
            <div class="col-md-4 animate-on-scroll delay-400">
                <div style="text-align:center;padding:40px 28px;border-radius:20px;background:var(--light);transition:all 0.3s;border-bottom:4px solid var(--success);">
                    <div style="width:80px;height:80px;background:linear-gradient(135deg,#27ae60,#2ecc71);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;font-size:2rem;">💎</div>
                    <h4 style="font-family:'Playfair Display',serif;margin-bottom:16px;">Our Values</h4>
                    <p style="color:var(--text-muted);font-size:14px;line-height:1.8;">Quality, sustainability, inclusivity, and innovation drive every decision we make. We believe fashion should empower, not compromise.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="section-pd" style="background:var(--light);">
    <div class="container">
        <div class="text-center mb-5 animate-on-scroll">
            <span class="section-tag">The Team</span>
            <h2 class="section-title">Meet Our <span>Designers</span></h2>
            <div class="divider-gold center"></div>
            <p class="section-subtitle">The passionate creatives behind every stunning collection.</p>
        </div>
        <div class="row g-4 justify-content-center">
            <?php
            $team = [
                ['name'=>'Zara Ahmed','role'=>'Creative Director','img'=>'https://images.unsplash.com/photo-1494790108755-2616b612b786?w=400&h=400&fit=crop'],
                ['name'=>'Hamza Sheikh','role'=>'Lead Designer','img'=>'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop'],
                ['name'=>'Nadia Malik','role'=>'Style Curator','img'=>'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=400&h=400&fit=crop'],
                ['name'=>'Ali Hassan','role'=>'Brand Manager','img'=>'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=400&fit=crop'],
            ];
            foreach ($team as $i => $m): ?>
            <div class="col-lg-3 col-md-6 animate-on-scroll delay-<?= $i * 100 ?>">
                <div class="team-card">
                    <div class="team-card-img">
                        <img src="<?= $m['img'] ?>" alt="<?= $m['name'] ?>" loading="lazy">
                        <div class="team-card-overlay">
                            <a href="#" class="team-social-link"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="team-social-link"><i class="fab fa-linkedin-in"></i></a>
                            <a href="#" class="team-social-link"><i class="fab fa-twitter"></i></a>
                        </div>
                    </div>
                    <div class="team-card-body">
                        <h5 class="team-name"><?= $m['name'] ?></h5>
                        <span class="team-role"><?= $m['role'] ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section style="padding:80px 0;background:linear-gradient(135deg,var(--primary),var(--primary-dark));">
    <div class="container text-center animate-on-scroll">
        <h2 style="font-family:'Playfair Display',serif;font-size:2.8rem;color:white;margin-bottom:20px;">Ready to Elevate Your <span style="color:var(--gold);">Style?</span></h2>
        <p style="color:rgba(255,255,255,0.7);max-width:500px;margin:0 auto 36px;">Explore thousands of curated fashion pieces and find your perfect look today.</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="products.php" class="btn-vv btn-gold">Shop Collection <i class="fas fa-arrow-right ms-2"></i></a>
            <a href="contact.php" class="btn-vv btn-outline-white">Get in Touch</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
