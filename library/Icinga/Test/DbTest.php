<?php
// {{{ICINGA_LICENSE_HEADER}}}
/**
 * This file is part of Icinga 2 Web.
 *
 * Icinga 2 Web - Head for multiple monitoring backends.
 * Copyright (C) 2013 Icinga Development Team
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * @copyright 2013 Icinga Development Team <info@icinga.org>
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt GPL, version 2
 * @author    Icinga Development Team <info@icinga.org>
 */
// {{{ICINGA_LICENSE_HEADER}}}

namespace Icinga\Test;

use \Zend_Db_Adapter_Pdo_Abstract;
use \Zend_Db_Adapter_Pdo_Mysql;
use \Zend_Db_Adapter_Pdo_Pgsql;
use \Zend_Db_Adapter_Pdo_Oci;

interface DbTest
{
    /**
     * PHPUnit provider for mysql
     *
     * @return Zend_Db_Adapter_Pdo_Mysql
     */
    public function mysqlDb();

    /**
     * PHPUnit provider for pgsql
     *
     * @return Zend_Db_Adapter_Pdo_Pgsql
     */
    public function pgsqlDb();

    /**
     * PHPUnit provider for oracle
     *
     * @return Zend_Db_Adapter_Pdo_Oci
     */
    public function oracleDb();

    /**
     * Executes sql file on PDO object
     *
     * @param   Zend_Db_Adapter_PDO_Abstract    $resource
     * @param   string                          $filename
     *
     * @return  boolean Operational success flag
     */
    public function loadSql(Zend_Db_Adapter_PDO_Abstract $resource, $filename);

    /**
     * Setup provider for testcase
     *
     * @param   string|Zend_Db_Adapter_PDO_Abstract|null $resource
     */
    public function setupDbProvider($resource);
}
