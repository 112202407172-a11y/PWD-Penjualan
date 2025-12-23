</div> <!-- Penutup content-wrapper -->
        
        <footer class="footer">
            <div class="footer-content">
                <div class="footer-left">
                    <p>&copy; <?php echo date('Y'); ?> Sistem Pendataan Barang. All rights reserved.</p>
                </div>
                <div class="footer-right">
                    <p>Version 1.0.0 | Made with <i class="fas fa-heart text-danger"></i></p>
                </div>
            </div>
        </footer>
        
        <script>
        // Update waktu secara real-time
        function updateDateTime() {
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            document.getElementById('current-date').textContent = 
                now.toLocaleDateString('id-ID', options);
        }
        
        // Update setiap detik
        setInterval(updateDateTime, 1000);
        updateDateTime();
        
        // Auto-hide notification setelah 5 detik
        setTimeout(function() {
            const notifications = document.querySelectorAll('.notification');
            notifications.forEach(notification => {
                notification.style.display = 'none';
            });
        }, 5000);
        </script>
    </div> <!-- Penutup container -->
</body>
</html>
