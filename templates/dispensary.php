<?php
script('aeneas_dispensary', 'dispensary');
/** @var array $_ */
$stats = $_['stats'];
$isAdmin = $_['isAdmin'] ?? false;
?>
<div id="aeneas-dispensary">
    <h2>Aeneas Dispensary – persönliche Abgabe</h2>

    <?php if ($isAdmin): ?>
    <div class="admin-notice box">
        <p><strong>Admin-Bereich</strong></p>
        <a href="<?php p(\OC::$server->getURLGenerator()->linkToRoute('aeneas_dispensary.dispensary.adminIndex')); ?>" class="button primary">
            Zur Admin-Übersicht
        </a>
    </div>
    <?php endif; ?>

    <p>Benutzer: <strong><?php p($_['userId']); ?></strong></p>

    <p>Heute: <?php p($stats['day']); ?> g / <?php p($stats['dailyLimit']); ?> g</p>
    <p>Diesen Monat: <?php p($stats['month']); ?> g / <?php p($stats['monthlyLimit']); ?> g</p>

    <div class="buttons">
        <button class="abgabe-btn" data-amount="1">+1 g</button>
        <button class="abgabe-btn" data-amount="5">+5 g</button>
        <button class="abgabe-btn" data-amount="10">+10 g</button>
    </div>

    <div id="aeneas-dispensary-message"></div>
</div>