<!-- ============ FOOTER ============ -->
<footer class="footer-main">
    <div class="container">
        <div class="row g-5">
            <!-- Brand -->
            <div class="col-lg-4 col-md-6">
                <span class="footer-logo">Velvet <span>Vogue</span></span>
                <p class="footer-about">
                    Redefining fashion with elegance and modernity. We bring you the finest curated collections that blend timeless style with contemporary trends.
                </p>
                <div class="footer-social">
                    <a href="#" class="social-btn fb" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-btn ig" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-btn tw" title="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-btn yt" title="YouTube"><i class="fab fa-youtube"></i></a>
                    <a href="#" class="social-btn" title="TikTok"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-2 col-md-3 col-6">
                <h6 class="footer-heading">Quick Links</h6>
                <ul class="footer-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="products.php">Shop All</a></li>
                    <li><a href="products.php?category=sale">Sale</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="blog.php">Fashion Blog</a></li>
                </ul>
            </div>

            <!-- Categories -->
            <div class="col-lg-2 col-md-3 col-6">
                <h6 class="footer-heading">Categories</h6>
                <ul class="footer-links">
                    <li><a href="products.php?category=women">Women</a></li>
                    <li><a href="products.php?category=men">Men</a></li>
                    <li><a href="products.php?category=kids">Kids</a></li>
                    <li><a href="products.php?category=accessories">Accessories</a></li>
                    <li><a href="products.php?category=sale">Sale</a></li>
                    <li><a href="products.php?new=1">New Arrivals</a></li>
                </ul>
            </div>

            <!-- Help -->
            <div class="col-lg-2 col-md-6 col-6 d-none d-md-block">
                <h6 class="footer-heading">Help</h6>
                <ul class="footer-links">
                    <li><a href="#">Size Guide</a></li>
                    <li><a href="#">Track Order</a></li>
                    <li><a href="#">Returns Policy</a></li>
                    <li><a href="#">Shipping Info</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                </ul>
            </div>

            <!-- Contact & Newsletter -->
            <div class="col-lg-2 col-md-6 col-6">
                <h6 class="footer-heading">Contact Us</h6>
                <div class="footer-contact-item">
                    <div class="footer-contact-icon"><i class="fas fa-map-marker-alt fa-sm"></i></div>
                    <span>123 Fashion Street, Karachi, Pakistan</span>
                </div>
                <div class="footer-contact-item">
                    <div class="footer-contact-icon"><i class="fas fa-phone fa-sm"></i></div>
                    <span>+92 300 1234567</span>
                </div>
                <div class="footer-contact-item">
                    <div class="footer-contact-icon"><i class="fas fa-envelope fa-sm"></i></div>
                    <span>info@velvetvogue.com</span>
                </div>

                <!-- Mini Newsletter -->
                <div style="margin-top:20px;">
                    <h6 class="footer-heading" style="font-size:12px;">Newsletter</h6>
                    <form class="newsletter-form-js" onsubmit="return false;">
                        <input type="email" class="footer-newsletter-input" placeholder="Your email address" required>
                        <button type="submit" class="btn-vv btn-gold w-100" style="padding:10px;font-size:13px;">
                            <i class="fas fa-paper-plane me-1"></i> Subscribe
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <p class="footer-copy mb-0">
                &copy; <?= date('Y') ?> <a href="index.php">Velvet Vogue</a>. All Rights Reserved. Crafted with <span style="color:#e74c3c;">♥</span>
            </p>
            <div class="footer-payments">
                <span class="payment-icon">VISA</span>
                <span class="payment-icon">MC</span>
                <span class="payment-icon">JazzCash</span>
                <span class="payment-icon">EasyPaisa</span>
                <span class="payment-icon">COD</span>
            </div>
        </div>
    </div>
</footer>

<!-- Back to Top -->
<button class="back-to-top" id="backToTop" title="Back to Top">
    <i class="fas fa-chevron-up"></i>
</button>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Main JS -->
<script src="<?= (strpos(basename($_SERVER['PHP_SELF']), 'admin') !== false || strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../' : '' ?>js/main.js"></script>

<?php if (isset($extraJS)) echo $extraJS; ?>
</body>
</html>
