<?php
// views/about.php - About page with owner profile & social links
$content = $content ?? [];
$aboutBg = $aboutBg ?? '';

$ownerName = $content['about.owner_name'] ?? 'Mirohood Studio';
$ownerRole = $content['about.owner_role'] ?? 'Photographer & Filmmaker';
$ownerBio = $content['about.owner_bio'] ?? 'Capturing authentic portraits and brand stories through light, framing, and a quiet attention to what makes each subject real.';
$ownerAvatar = $content['about.owner_avatar'] ?? '';
$instagram = !empty($content['about.instagram']) ? ltrim($content['about.instagram'], '@') : '';
$youtube = $content['about.owner_social_youtube'] ?? '';
$vimeo = $content['about.owner_social_vimeo'] ?? '';
$twitter = $content['about.owner_social_twitter'] ?? '';
$email = $content['about.email'] ?? (defined('SITE_EMAIL') ? SITE_EMAIL : 'Parsmiro@gmail.com');
$website = $content['about.website'] ?? '';
?>
<style>
    .about-page {
        position: relative;
        z-index: 1;
        padding: 5rem 1.5rem 7rem;
        min-height: 80vh;
        overflow: hidden;
    }
    .about-page::before {
        content: '';
        position: fixed;
        inset: 0;
        z-index: -2;
        <?php if (!empty($aboutBg)): ?>
        background-image: url('<?php echo h($aboutBg); ?>');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        background-repeat: no-repeat;
        <?php else: ?>
        background: radial-gradient(ellipse at 30% 20%, rgba(200,168,98,0.04), transparent 50%),
                    radial-gradient(ellipse at 80% 80%, rgba(168,137,58,0.03), transparent 50%),
                    #0a0908;
        <?php endif; ?>
    }
    .about-page::after {
        content: '';
        position: fixed;
        inset: 0;
        z-index: -1;
        background: rgba(10,9,8,0.82);
        backdrop-filter: blur(8px);
    }
    .about-container {
        max-width: 1100px;
        margin: 0 auto;
        position: relative;
        z-index: 1;
    }

    /* Hero */
    .about-hero {
        text-align: center;
        margin-bottom: 4rem;
    }
    .about-eyebrow {
        display: inline-block;
        font-size: 0.7rem;
        letter-spacing: 0.2em;
        color: #c8a862;
        text-transform: uppercase;
        margin-bottom: 0.75rem;
        border: 1px solid rgba(200,168,98,0.15);
        padding: 0.25rem 1rem;
        border-radius: 9999px;
    }
    .about-title {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: clamp(2.5rem, 6vw, 4.5rem);
        font-weight: 300;
        color: #f4f1ea;
        margin: 0 0 1rem;
        line-height: 1.1;
    }
    .about-title span { color: #c8a862; }
    .about-intro {
        color: #8a8580;
        font-size: 1.05rem;
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.8;
    }

    /* Owner Profile Card */
    .about-owner-card {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 2.5rem;
        align-items: center;
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(200,168,98,0.1);
        border-radius: 1.5rem;
        padding: 2.5rem;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        box-shadow: 0 30px 80px rgba(0,0,0,0.4), inset 0 1px 0 rgba(255,255,255,0.02);
        margin-bottom: 4rem;
    }
    .about-owner-visual {
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .about-owner-frame {
        width: 240px;
        height: 240px;
        border-radius: 50%;
        padding: 6px;
        border: 2px solid rgba(200,168,98,0.3);
        background: linear-gradient(135deg, rgba(200,168,98,0.12), rgba(168,137,58,0.06));
        box-shadow: 0 20px 60px rgba(0,0,0,0.4), 0 0 40px rgba(200,168,98,0.08);
        position: relative;
    }
    .about-owner-frame::before {
        content: '';
        position: absolute;
        inset: -12px;
        border-radius: 50%;
        border: 1px solid rgba(200,168,98,0.08);
        pointer-events: none;
    }
    .about-owner-frame img,
    .about-owner-frame .about-owner-initial {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        display: block;
    }
    .about-owner-initial {
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(200,168,98,0.08);
        color: #c8a862;
        font-size: 5rem;
    }
    .about-owner-name {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: clamp(2rem, 4vw, 2.8rem);
        font-weight: 300;
        color: #f4f1ea;
        margin: 0 0 0.3rem;
    }
    .about-owner-role {
        display: inline-block;
        color: #c8a862;
        font-size: 0.75rem;
        letter-spacing: 0.15em;
        text-transform: uppercase;
        margin-bottom: 1.25rem;
        border-bottom: 1px solid rgba(200,168,98,0.2);
        padding-bottom: 0.4rem;
    }
    .about-owner-bio {
        color: #8a8580;
        font-size: 1rem;
        line-height: 1.9;
        margin-bottom: 1.5rem;
    }
    .about-owner-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }
    .about-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.65rem 1.6rem;
        border-radius: 9999px;
        font-size: 0.8rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 1px solid;
    }
    .about-btn-primary {
        background: linear-gradient(135deg, #c8a862, #a8893a);
        border-color: transparent;
        color: #0a0908;
    }
    .about-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(200,168,98,0.25);
    }
    .about-btn-outline {
        background: transparent;
        border-color: rgba(200,168,98,0.2);
        color: #f4f1ea;
    }
    .about-btn-outline:hover {
        border-color: #c8a862;
        color: #c8a862;
        background: rgba(200,168,98,0.06);
    }

    /* Social Links */
    .about-socials {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    .about-social {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1.1rem;
        border-radius: 9999px;
        border: 1px solid rgba(255,255,255,0.08);
        background: rgba(255,255,255,0.03);
        color: #f4f1ea;
        font-size: 0.8rem;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    .about-social:hover {
        transform: translateY(-2px);
        border-color: rgba(200,168,98,0.3);
    }
    .about-social.instagram:hover { color: #e1306c; border-color: rgba(225,48,108,0.4); background: rgba(225,48,108,0.08); }
    .about-social.youtube:hover { color: #ff0000; border-color: rgba(255,0,0,0.4); background: rgba(255,0,0,0.08); }
    .about-social.vimeo:hover { color: #1ab7ea; border-color: rgba(26,183,234,0.4); background: rgba(26,183,234,0.08); }
    .about-social.twitter:hover { color: #1da1f2; border-color: rgba(29,161,242,0.4); background: rgba(29,161,242,0.08); }
    .about-social.email:hover { color: #c8a862; border-color: rgba(200,168,98,0.4); background: rgba(200,168,98,0.08); }
    .about-social.website:hover { color: #c8a862; border-color: rgba(200,168,98,0.4); background: rgba(200,168,98,0.08); }

    /* Quote */
    .about-quote {
        position: relative;
        text-align: center;
        margin: 4rem 0;
        padding: 2.5rem 2rem;
        background: rgba(255,255,255,0.02);
        border: 1px solid rgba(200,168,98,0.08);
        border-radius: 1rem;
    }
    .about-quote::before {
        content: '“';
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 6rem;
        color: rgba(200,168,98,0.15);
        position: absolute;
        top: -1.5rem;
        right: 1.5rem;
        line-height: 1;
    }
    .about-quote p {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: clamp(1.5rem, 3vw, 2.2rem);
        font-weight: 300;
        color: #f4f1ea;
        line-height: 1.5;
        margin: 0;
        position: relative;
        z-index: 1;
    }

    /* Body text */
    .about-body {
        max-width: 800px;
        margin: 0 auto 4rem;
        text-align: center;
    }
    .about-body p {
        color: #8a8580;
        font-size: 1.05rem;
        line-height: 2;
    }

    /* Contact Grid */
    .about-contact-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        max-width: 800px;
        margin: 0 auto 4rem;
    }
    .about-contact-card {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(200,168,98,0.08);
        border-radius: 1rem;
        padding: 1.75rem;
        transition: all 0.3s ease;
    }
    .about-contact-card:hover {
        border-color: rgba(200,168,98,0.15);
        background: rgba(255,255,255,0.05);
        transform: translateY(-3px);
    }
    .about-contact-card h3 {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 1.2rem;
        font-weight: 300;
        color: #c8a862;
        margin: 0 0 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .about-contact-card p {
        color: #f4f1ea;
        font-size: 0.95rem;
        line-height: 1.8;
        margin: 0;
    }

    /* CTA */
    .about-cta {
        text-align: center;
    }
    .about-cta .about-btn-primary {
        font-size: 0.9rem;
        padding: 0.8rem 2.5rem;
    }

    /* Responsive */
    @media (max-width: 900px) {
        .about-owner-card {
            grid-template-columns: 1fr;
            text-align: center;
            padding: 2rem 1.5rem;
        }
        .about-owner-actions, .about-socials {
            justify-content: center;
        }
        .about-contact-grid {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 480px) {
        .about-owner-frame {
            width: 180px;
            height: 180px;
        }
    }
</style>

<section class="about-page">
    <div class="about-container">

        <div class="about-hero">
            <span class="about-eyebrow"><?php echo h($content['about.eyebrow'] ?? 'About'); ?></span>
            <h1 class="about-title"><?php echo h($content['about.title'] ?? 'Mirohood'); ?> <span>Studio</span></h1>
            <p class="about-intro"><?php echo h($content['about.intro'] ?? 'Portrait photography & brand filming — based on Earth, rooted in light.'); ?></p>
        </div>

        <div class="about-owner-card">
            <div class="about-owner-visual">
                <div class="about-owner-frame">
                    <?php if (!empty($ownerAvatar)): ?>
                        <img src="<?php echo h($ownerAvatar); ?>" alt="<?php echo h($ownerName); ?>" loading="eager" decoding="async">
                    <?php else: ?>
                        <div class="about-owner-initial"><i class="fas fa-user"></i></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="about-owner-content">
                <h2 class="about-owner-name"><?php echo h($ownerName); ?></h2>
                <span class="about-owner-role"><?php echo h($ownerRole); ?></span>
                <p class="about-owner-bio"><?php echo h($ownerBio); ?></p>

                <div class="about-owner-actions">
                    <a href="<?php echo url('booking'); ?>" class="about-btn about-btn-primary">
                        <i class="fas fa-calendar-check"></i>
                        <?php echo h($content['about.button'] ?? 'Book a Session'); ?>
                    </a>
                    <?php if (!empty($email)): ?>
                        <a href="mailto:<?php echo h($email); ?>" class="about-btn about-btn-outline">
                            <i class="fas fa-envelope"></i>
                            <?php echo h($email); ?>
                        </a>
                    <?php endif; ?>
                </div>

                <div class="about-socials">
                    <?php if (!empty($instagram)): ?>
                        <a href="https://instagram.com/<?php echo h($instagram); ?>" target="_blank" rel="noopener" class="about-social instagram">
                            <i class="fab fa-instagram"></i> @<?php echo h($instagram); ?>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($youtube)): ?>
                        <a href="<?php echo h($youtube); ?>" target="_blank" rel="noopener" class="about-social youtube">
                            <i class="fab fa-youtube"></i> YouTube
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($vimeo)): ?>
                        <a href="<?php echo h($vimeo); ?>" target="_blank" rel="noopener" class="about-social vimeo">
                            <i class="fab fa-vimeo-v"></i> Vimeo
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($twitter)): ?>
                        <a href="<?php echo h($twitter); ?>" target="_blank" rel="noopener" class="about-social twitter">
                            <i class="fab fa-twitter"></i> Twitter
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($website)): ?>
                        <a href="<?php echo h($website); ?>" target="_blank" rel="noopener" class="about-social website">
                            <i class="fas fa-globe"></i> Website
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <?php if (!empty($content['about.quote'])): ?>
            <div class="about-quote">
                <p><?php echo h($content['about.quote']); ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($content['about.body'])): ?>
            <div class="about-body">
                <p><?php echo h($content['about.body']); ?></p>
            </div>
        <?php endif; ?>

        <div class="about-contact-grid">
            <div class="about-contact-card">
                <h3><i class="fas fa-map-marker-alt"></i> <?php echo h($content['about.address_label'] ?? 'Studio Address'); ?></h3>
                <p><?php echo nl2br(h($content['about.address'] ?? 'Ahvaz, Iran')); ?></p>
            </div>
            <div class="about-contact-card">
                <h3><i class="fas fa-phone-alt"></i> <?php echo h($content['about.contact_label'] ?? 'Get in Touch'); ?></h3>
                <p><?php echo nl2br(h($content['about.contact'] ?? "+98 21 0000 0000\nhello@mirohood.ir")); ?></p>
            </div>
        </div>

        <div class="about-cta">
            <a href="<?php echo url('booking'); ?>" class="about-btn about-btn-primary">
                <i class="fas fa-calendar-check"></i>
                <?php echo h($content['about.button'] ?? 'Book a Session'); ?>
            </a>
        </div>

    </div>
</section>
