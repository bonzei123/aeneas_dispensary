<?php
// Wichtig: Das JavaScript auch in der Admin-Ansicht laden!
script('aeneas_dispensary', 'dispensary');
style('aeneas_dispensary', 'dispensary');

/** @var array $_ */
$entries = $_['entries'];
?>

<div id="aeneas-dispensary-admin">

    <div class="section dispensary-card">

        <div style="margin-bottom: var(--default-grid-unit);">
            <a href="<?php p(\OC::$server->getURLGenerator()->linkToRoute('aeneas_dispensary.dispensary.index')); ?>"
               class="button">
                &laquo; Zurück zur Abgabe
            </a>
        </div>

        <h2>Aeneas Dispensary – Admin Übersicht</h2>

        <table class="grid">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Menge (g)</th>
                    <th>Timestamp</th>
                    <th>Bearbeitet von</th>
                    <th>Bearbeitet am</th>
                    <th>Aktion</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($entries as $e): ?>
                    <tr>
                        <td><?php p($e->getId()); ?></td>
                        <td><?php p($e->getUserId()); ?></td>
                        <td><?php p($e->getAmount()); ?></td>
                        <td><?php p(date('Y-m-d H:i', $e->getTimestamp())); ?></td>
                        <td><?php p($e->getEditedBy()); ?></td>
                        <td><?php p($e->getEditedAt() ? date('Y-m-d H:i', $e->getEditedAt()) : ''); ?></td>

                        <td>
                            <button class="edit-abgabe-btn"
                                    data-id="<?php p($e->getId()); ?>"
                                    data-current="<?php p($e->getAmount()); ?>">
                                Bearbeiten
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div id="aeneas-admin-message" class="admin-message"></div>

    </div>

</div>
