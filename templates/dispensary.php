<?php
script('aeneas_dispensary', 'dispensary');
style('aeneas_dispensary', 'dispensary');

/** @var array $_ */
$stats = $_['stats'];
$isAdmin = $_['isAdmin'] ?? false;
$userId = $_['userId'];
?>

<div id="aeneas-dispensary">

    <div class="section dispensary-card">
        <h2>Aeneas Dispensary – persönliche Abgabe</h2>

        <?php if ($isAdmin): ?>
            <div class="box admin-notice" style="margin-bottom: var(--default-grid-unit);">
                <p><strong>Admin-Bereich</strong></p>
                <a href="<?php p(\OC::$server->getURLGenerator()->linkToRoute('aeneas_dispensary.dispensary.adminIndex')); ?>"
                   class="button primary">
                    Zur Admin-Übersicht
                </a>
            </div>
        <?php endif; ?>

        <p>Benutzer: <strong><?php p($userId); ?></strong></p>

        <p>
            Heute:
            <strong><?php p($stats['day']); ?> g</strong>
            /
            <?php p($stats['dailyLimit']); ?> g
        </p>

        <p>
            Diesen Monat:
            <strong><?php p($stats['month']); ?> g</strong>
            /
            <?php p($stats['monthlyLimit']); ?> g
        </p>

        <div class="buttons">
            <button class="abgabe-btn" data-amount="1">+1 g</button>
            <button class="abgabe-btn" data-amount="5">+5 g</button>
            <button class="abgabe-btn" data-amount="10">+10 g</button>
        </div>

        <div id="aeneas-dispensary-message"></div>
    </div>

</div>
