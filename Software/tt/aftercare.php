<?php
session_start();
include 'config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aftercare Guide - Tattoo Verse</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include 'includes/nav.php'; // Include the centralized navigation bar ?>
 
    <main class="aftercare-page">
        <section class="aftercare-header">
            <div class="container">
                <h1>Tattoo Aftercare Guide</h1>
                <p class="header-desc">Your complete guide to tattoo healing and maintenance</p>
            </div>
        </section>

        <section class="aftercare-content">
            <div class="container">
                <div class="video-tutorials">
                    <h2>Essential Aftercare Videos</h2>
                    <div class="video-grid">
                        <div class="video-card">
                            <h3>How to Clean Your New Tattoo</h3>
                            <div class="video-container">
                                <iframe width="560" height="315" src="https://www.youtube.com/embed/yroOhdfqnpo" title="How to Clean Your New Tattoo" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                        </div>
                        <div class="video-card">
                            <h3>Proper Moisturizing Technique</h3>
                            <div class="video-container">
                                <iframe width="560" height="315" src="https://www.youtube.com/embed/mtwBo4otKrg" title="How to Moisturize Your Tattoo" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="healing-timeline">
                    <h2>Healing Timeline</h2>
                    
                    <div class="timeline-grid">
                        <div class="timeline-card">
                            <div class="card-header">
                                <h3>Day 1</h3>
                            </div>
                            <div class="card-content">
                                <ol>
                                    <li>Keep the bandage on for 2-4 hours</li>
                                    <li>Wash hands thoroughly before touching tattoo</li>
                                    <li>Clean with lukewarm water and gentle soap</li>
                                    <li>Pat dry with clean paper towels</li>
                                    <li>Apply thin layer of aftercare ointment</li>
                                </ol>
                            </div>
                        </div>

                        <div class="timeline-card">
                            <div class="card-header">
                                <h3>Week 1</h3>
                            </div>
                            <div class="card-content">
                                <ol>
                                    <li>Clean 2-3 times daily</li>
                                    <li>Use recommended aftercare products</li>
                                    <li>Wear loose clothing</li>
                                    <li>Avoid swimming and direct sunlight</li>
                                    <li>Don't pick or scratch</li>
                                </ol>
                            </div>
                        </div>

                        <div class="timeline-card">
                            <div class="card-header">
                                <h3>Week 2-4</h3>
                            </div>
                            <div class="card-content">
                                <ol>
                                    <li>Continue gentle cleaning</li>
                                    <li>Use unscented lotion</li>
                                    <li>Protect from sun exposure</li>
                                    <li>Avoid tight clothing</li>
                                    <li>Let skin heal naturally</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="important-notes">
                    <h2>Important Notes</h2>
                    <div class="notes-grid">
                        <div class="note">
                            <i class="fas fa-exclamation-triangle"></i>
                            <h3>Don't</h3>
                            <ul>
                                <li>Scratch or pick at the tattoo</li>
                                <li>Expose to direct sunlight</li>
                                <li>Swim or soak in water</li>
                                <li>Use harsh chemicals</li>
                                <li>Wear tight clothing over it</li>
                            </ul>
                        </div>

                        <div class="note">
                            <i class="fas fa-check-circle"></i>
                            <h3>Do</h3>
                            <ul>
                                <li>Keep the area clean</li>
                                <li>Use recommended products</li>
                                <li>Wear loose clothing</li>
                                <li>Let it breathe</li>
                                <li>Contact us if concerned</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="products-section">
                    <h2>Recommended Products</h2>
                    
                    <div class="products-grid">
                        <div class="product-card">
                            <div class="product-image">
                                <img src="images/artists/soap.jpg" alt="H2Ocean Blue Green Foam Soap">
                            </div>
                            <div class="product-info">
                                <h3>H2Ocean Blue Green Foam Soap</h3>
                                <div class="product-details">
                                    <div class="info-row">
                                        <span class="label">Use For:</span>
                                        <span class="value">Daily Cleaning (Days 1-14)</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="label">Benefits:</span>
                                        <ul>
                                            <li>pH balanced for tattoos</li>
                                            <li>Prevents infection</li>
                                            <li>Reduces redness</li>
                                        </ul>
                                    </div>
                                    <div class="usage-steps">
                                        <p class="step">1. Pump foam onto clean hands</p>
                                        <p class="step">2. Gently apply to tattoo</p>
                                        <p class="step">3. Rinse with warm water</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="product-card">
                            <div class="product-image">
                                <img src="images/artists/body.jpg" alt="Hustle Butter Deluxe">
                            </div>
                            <div class="product-info">
                                <h3>Hustle Butter Healer</h3>
                                <div class="product-details">
                                    <div class="info-row">
                                        <span class="label">Use For:</span>
                                        <span class="value"> Healing (Days 3-30)</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="label">Benefits:</span>
                                        <ul>
                                            <li>100% natural ingredients</li>
                                            <li>Speeds up healing</li>
                                            <li>Preserves color vibrancy</li>
                                        </ul>
                                    </div>
                                    <div class="usage-steps">
                                        <p class="step">1. Clean hands thoroughly</p>
                                        <p class="step">2. Apply very thin layer</p>
                                        <p class="step">3. Use 2-3 times daily</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="product-card">
                            <div class="product-image">
                                <img src="images/artists/lotion.jpg" alt=" FlupLotion">
                            </div>
                            <div class="product-info">
                                <h3>After Inked Tattoo Lotion</h3>
                                <div class="product-details">
                                    <div class="info-row">
                                        <span class="label">Use For:</span>
                                        <span class="value">Long-term Care (Week 2+)</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="label">Benefits:</span>
                                        <ul>
                                            <li>Non-petroleum based</li>
                                            <li>Vegan friendly</li>
                                            <li>Daily moisturizing</li>
                                        </ul>
                                    </div>
                                    <div class="usage-steps">
                                        <p class="step">1. Ensure tattoo is clean</p>
                                        <p class="step">2. Apply small amount</p>
                                        <p class="step">3. Use daily as needed</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="product-note">
                        <i class="fas fa-info-circle"></i>
                        <p>These are professional recommendations only. Products can be purchased from your local tattoo supply store or authorized retailers.</p>
                    </div>
                </div>

                <div class="contact-section">
                    <h2>Need Help?</h2>
                    <p>If you notice any unusual symptoms or have concerns about your healing process, please don't hesitate to contact us:</p>
                    <div class="contact-info">
                        <div><i class="fas fa-phone"></i> +919588423829</div>
                        <div><i class="fas fa-envelope"></i> okmy1337r@gmail.com</div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="footer">
  <div class="container footer-content">
    <p class="footer-address">
      <i class="fas fa-map-marker-alt"></i>
      133 Mumbai Dotson Gullies, Near SSK School, Mumbai(W) 421503
    </p>
  </div>
</footer>

    <script src="js/main.js"></script>
</body>
</html>
