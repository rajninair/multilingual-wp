jQuery(document).ready(function($) {
    // Get the video element
    const video = document.getElementById('myVideo');
    
    if (video) {
        // Handle video loading
        video.addEventListener('loadeddata', function() {
            video.play().catch(function(error) {
                console.log("Video autoplay failed:", error);
            });
        });

        // Handle visibility changes
        document.addEventListener("visibilitychange", function() {
            if (document.hidden) {
                video.pause();
            } else {
                video.play().catch(function(error) {
                    console.log("Video autoplay failed:", error);
                });
            }
        });

        // Ensure video stays full screen
        function resizeVideo() {
            const videoRatio = video.videoWidth / video.videoHeight;
            const windowRatio = window.innerWidth / window.innerHeight;
            
            if (windowRatio > videoRatio) {
                video.style.width = '100vw';
                video.style.height = 'auto';
            } else {
                video.style.width = 'auto';
                video.style.height = '100vh';
            }
        }

        // Resize video on load and window resize
        window.addEventListener('resize', resizeVideo);
        video.addEventListener('loadedmetadata', resizeVideo);
    }
}); 