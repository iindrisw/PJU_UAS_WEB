<!-- Bootstrap 5 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Leaflet.js -->
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
  <!-- Custom JS -->
  <script src="<?= BASE_URL ?>/assets/js/app.js"></script>
  <?php if (isset($extraJs)) echo $extraJs; ?>
  <?php
  // Tampilkan flash message jika ada
  $flash = getFlash();
  if ($flash): ?>
  <script>
    Swal.fire({
      icon: '<?= $flash['type'] === 'success' ? 'success' : ($flash['type'] === 'error' ? 'error' : 'info') ?>',
      title: '<?= $flash['type'] === 'success' ? 'Berhasil!' : ($flash['type'] === 'error' ? 'Gagal!' : 'Info') ?>',
      text: '<?= e($flash['msg']) ?>',
      timer: 3000,
      showConfirmButton: false
    });
  </script>
  <?php endif; ?>
</body>
</html>