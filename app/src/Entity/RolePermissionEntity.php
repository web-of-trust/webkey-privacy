<?php declare(strict_types=1);

namespace CodeIgniter\Gate\Entities;

/**
 * Role permission entity class
 * 
 * @package    CodeIgniter
 * @subpackage Gate\Entities
 * @author     Nguyen Van Nguyen - nguyennv1981@gmail.com
 * @copyright  Copyright © 2022-present by Nguyen Van Nguyen
 */
class RolePermissionEntity extends BaseEntity {
    public const TABLE_NAME  = 'role_permissions';
    public const ID_COLUMN   = 'role_permissions.id';
    public const ROLE_ID_COLUMN = 'role_permissions.role_id';
    public const PERMISSION_COLUMN = 'role_permissions.permission';

    protected $attributes = [
        'id' => null,
        'role_id' => 0,
        'permission' => null,
        'created_by' => 0,
        'created_at' => null,
    ];
}
