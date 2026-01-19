<?php
// Partial view: saved items indicator
$isLoggedIn = isset($_SESSION['user_id']);
$total = 0;

if ($isLoggedIn) {
$total = function_exists('saved_total_count') ? saved_total_count() : 0;
}
?>

<?php if ($isLoggedIn): ?>
    <a class="nav-link position-relative" href="index.php?action=saved">
        <i class="bi bi-trash3 me-1"></i>Saved
        <?php if ($total > 0): ?>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark">
                <?php echo (int)$total; ?>
            </span>
        <?php endif; ?>
    </a>
<?php endif; ?>
