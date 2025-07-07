<?php
session_start();
include 'config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery - Tattoo Verse</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include 'includes/nav.php'; // Include the centralized navigation bar ?>

    <main class="gallery-page">
        <!-- Artists Section -->
        <section class="artists-showcase">
            <div class="container">
                <h2>Our Artists</h2>
                <div class="artists-grid">
                    <?php
                    try {
                        // Fetch users with the 'artist' role
                        $stmt = $pdo->query("SELECT id, username, specialization, image_path FROM users WHERE role = 'artist' ORDER BY username ASC");
                        $artists = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        if ($artists) {
                            foreach ($artists as $artist) {
                                echo '<div class="artist-card">';
                                // Display artist image if available
                                if (!empty($artist['image_path']) && file_exists($artist['image_path'])) {
                                    echo '<img src="' . htmlspecialchars($artist['image_path']) . '" alt="' . htmlspecialchars($artist['username']) . '" class="artist-image">';
                                } else {
                                    echo '<div class="artist-image-placeholder">No Image Available</div>';
                                }
                                echo '<h3>' . htmlspecialchars($artist['username']) . '</h3>';
                                // Display specialization if available
                                if (!empty($artist['specialization'])) {
                                    echo '<p>' . htmlspecialchars($artist['specialization']) . '</p>';
                                    // Optional: You could split specialization string into tags if needed
                                    echo '<div class="specialties">';
                                    $specialties = explode(',', $artist['specialization']); // Assuming comma-separated
                                    foreach($specialties as $spec) {
                                        echo '<span class="style-tag">' . htmlspecialchars(trim($spec)) . '</span>';
                                    }
                                    echo '</div>';
                                }
                                echo '</div>'; // end artist-card
                            }
                        } else {
                            echo '<p>No artists currently available.</p>';
                        }
                    } catch(PDOException $e) {
                        echo '<p>Error loading artists. Please try again later.</p>';
                        error_log("Gallery Page Error: PDOException - " . $e->getMessage());
                    }
                    ?>
                </div>
            </div>
        </section>

        <!-- Tattoo Styles Gallery -->
        <section class="tattoo-gallery">
            <div class="container">
                <h2>Tattoo Styles</h2>
                <p class="section-desc">Explore our diverse collection of tattoo styles and find the perfect design for you</p>
                
                <div class="gallery-grid">
                    <div class="gallery-item">
                        <img src="images/artists/jap.jpg" alt="Japanese Dragon Tattoo">
                        <div class="gallery-info">
                            <h3>Japanese Style</h3>
                            <p>Artist: Hardik</p>
                            <span class="style-tag">Traditional Japanese</span>
                        </div>
                    </div>
                    
                    <div class="gallery-item">
                        <img src="images/artists/lion.jpg" alt="Realistic Lion Portrait">
                        <div class="gallery-info">
                            <h3>Realistic Portrait</h3>
                            <p>Artist: Kiran</p>
                            <span class="style-tag">Realism</span>
                        </div>
                    </div>
                    
                    <div class="gallery-item">
                        <img src="images/artists/deer.jpg" alt="Geometric Deer Design">
                        <div class="gallery-info">
                            <h3>Geometric Design</h3>
                            <p>Artist: Hardik</p>
                            <span class="style-tag">Geometric</span>
                        </div>
                    </div>
                    
                    <div class="gallery-item">
                        <img src="images/artists/rose.jpg" alt="Neo Traditional Rose">
                        <div class="gallery-info">
                            <h3>Neo Traditional</h3>
                            <p>Artist: Tulsidas Khan</p>
                            <span class="style-tag">Neo Traditional</span>
                        </div>
                    </div>
                    
                    <div class="gallery-item">
                        <img src="images/artists/mand.jpg" alt="Blackwork Mandala">
                        <div class="gallery-info">
                            <h3>Blackwork Mandala</h3>
                            <p>Artist: Harry</p>
                            <span class="style-tag">Blackwork</span>
                        </div>
                    </div>
                    
                    <div class="gallery-item">
                        <img src="images/artists/phe.jpg" alt="Watercolor Phoenix">
                        <div class="gallery-info">
                            <h3>Watercolor Style</h3>
                            <p>Artist: Tulsidas Khan</p>
                            <span class="style-tag">Watercolor</span>
                        </div>
                    </div>
                    
                    <div class="gallery-item">
                        <img src="images/artists/real.jpg" alt="Realistic Portrait">
                        <div class="gallery-info">
                            <h3>Portrait Work</h3>
                            <p>Artist: Hardik</p>
                            <span class="style-tag">Realism</span>
                        </div>
                    </div>
                    
                    <div class="gallery-item">
                        <img src="images/artists/tribal.jpg" alt="Tribal Maori Design">
                        <div class="gallery-info">
                            <h3>Tribal Maori</h3>
                            <p>Artist: Adarsh</p>
                            <span class="style-tag">Tribal</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script src="js/main.js"></script>
</body>
</html>

<style>
.artist-image {
    width: 100%; /* Make image responsive within the card */
    height: 200px; /* Fixed height for consistency */
    object-fit: cover; /* Cover the area, cropping if necessary */
    border-radius: 5px; /* Slightly rounded corners for the image */
    margin-bottom: 1rem;
    background-color: #f0f0f0; /* Background for loading or if image is missing */
}

.artist-image-placeholder {
    width: 100%;
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #e9ecef;
    color: #6c757d;
    font-style: italic;
    border-radius: 5px;
    margin-bottom: 1rem;
}
</style>
