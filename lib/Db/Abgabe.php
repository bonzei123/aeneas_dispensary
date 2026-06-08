<?php

declare(strict_types=1);

namespace OCA\AeneasDispensary\Db;

use OCP\AppFramework\Db\Entity;

/**
 * @method void setUserId(string $userId)
 * @method string getUserId()
 * @method void setAmount(int $amount)
 * @method int getAmount()
 * @method void setTimestamp(int $timestamp)
 * @method int getTimestamp()
 * @method void setEditedBy(?string $editedBy)
 * @method ?string getEditedBy()
 * @method void setEditedAt(?int $editedAt)
 * @method ?int getEditedAt()
 */
class Abgabe extends Entity {
    protected $userId;
    protected $amount;
    protected $timestamp;
    protected $editedBy;
    protected $editedAt;

    public function __construct() {
        $this->addType('userId', 'string');
        $this->addType('amount', 'int');
        $this->addType('timestamp', 'int');
        $this->addType('editedBy', 'string');
        $this->addType('editedAt', 'int');
    }
}
