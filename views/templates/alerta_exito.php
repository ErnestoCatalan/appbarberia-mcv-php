<?php if (isset($_SESSION['exito'])): ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: "success",
            title: "¡Éxito!",
            text: "<?php echo $_SESSION['exito']; ?>",
            confirmButtonText: "OK"
        });
    </script>
    <?php unset($_SESSION['exito']); ?>
<?php endif; ?>
