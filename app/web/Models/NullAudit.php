<?php
/**
 * Karaka
 *
 * PHP Version 8.1
 *
 * @package   Modules\Auditor\Models
 * @copyright Dennis Eichhorn
 * @license   OMS License 1.0
 * @version   1.0.0
 * @link      https://karaka.app
 */
declare(strict_types=1);

namespace Models;

final class NullAudit extends Audit
{
    public function __construct(int $id = 0)
    {
        $this->id = $id;
        parent::__construct();
    }
}
