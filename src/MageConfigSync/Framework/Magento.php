<?php

namespace MageConfigSync\Framework;

use Aura\Sql\ExtendedPdo;
use MageConfigSync\Config\ConfigItemSet\Processor;
use PDO;

class Magento extends Processor
{
    /**
     * @var \PDO
     */
    private $pdo;
    
    /**
     * @var \PDOStatement
     */
    private $upsertStatement;
    
    /**
     * @var \PDOStatement
     */
    private $deleteStatement;
    
    /**
     * @var string
     */
    private $tablePrefix;
    
    /**
     * MagentoOne constructor.
     *
     * @param \PDO   $pdo
     * @param string $tablePrefix
     */
    public function __construct(ExtendedPdo $pdo, $tablePrefix = '')
    {
        $this->pdo = $pdo;
        $this->tablePrefix = $tablePrefix;
        
        $this->upsertStatement = $this->pdo->prepare('
            INSERT INTO `' . $this->getTablename() . '` (scope, scope_id, path, value)
            VALUES (:scope, :scope_id, :path, :value)
            ON DUPLICATE KEY UPDATE value = :value
        ');
        
        $this->deleteStatement = $this->pdo->prepare(
            'DELETE FROM `' . $this->getTablename() . '` WHERE scope = :scope AND scope_id = :scope_id AND path = :path'
        );
        $this->tablePrefix = $tablePrefix;
    }
    
    /**
     * @return string
     */
    protected function getTablename()
    {
        return sprintf('%score_config_data', $this->tablePrefix);
    }
    
    /**
     * @param $scope
     * @param $key
     *
     * @return int Affected rows
     */
    protected function deleteConfig($scope, $key)
    {
        $statement = $this->deleteStatement;
        
        $statement->bindParam(':scope', $this->getScopeName($scope), PDO::PARAM_STR);
        $statement->bindParam(':scope_id', $this->getScopeId($scope), PDO::PARAM_INT);
        $statement->bindParam(':path', $key, PDO::PARAM_STR);
        
        return $statement->execute() ? $statement->rowCount() : 0;
    }
    
    /**
     * @param $scope
     * @param $key
     * @param $value
     *
     * @return int Affected rows
     */
    protected function upsertConfig($scope, $key, $value)
    {
        $statement = $this->upsertStatement;
        
        $statement->bindParam(':scope', $this->getScopeName($scope), PDO::PARAM_STR);
        $statement->bindParam(':scope_id', $this->getScopeId($scope), PDO::PARAM_INT);
        $statement->bindParam(':path', $key, PDO::PARAM_STR);
        $statement->bindParam(':value', $value, PDO::PARAM_STR);
        
        return $statement->execute() ? $statement->rowCount() : 0;
    }
    
    public function generateConfigItemSet()
    {
        
    }
    
    /**
     * @param $scope
     * @return mixed
     */
    protected function getScopeName($scope)
    {
        $scopeParts = explode('-', $scope);
        if (count($scopeParts) == 2) {
            return $scopeParts[0];
        }
        
        return 'default';
    }
    
    /**
     * @param $scope
     * @return int
     */
    protected function getScopeId($scope)
    {
        $scopeParts = explode('-', $scope);
        if (count($scopeParts) == 2) {
            return (int) $scopeParts[1];
        }
        
        return 0;
    }
}