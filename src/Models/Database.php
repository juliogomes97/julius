<?php declare(strict_types=1);

namespace Julius\Framework\Models;

use \Exception;
use \PDO;
use \PDOStatement;
use \PDOException;

final class Database
{
    private PDO $handler;
    private PDOStatement | false $statement;
    
    public function __construct(string $host, string $user, string $password, string $database, int $port = 3306, array $options = [
        PDO::ATTR_PERSISTENT    => true,
        PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION
    ])
    {
        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8";

        try
        {
            $this->handler = new PDO($dsn, $user, $password, $options);
        }
        catch (PDOException $e)
        {
            $this->handleError($e);
        }
    }

    public function query(string $query, array $options = []) : void
    {
        $this->statement = $this->handler->prepare($query, $options);
    }

    public function bind(string $param, mixed $value, int $type = null) : void
    {
        if (is_null($type))
        {
            switch (true)
            {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
                    break;
            }
        }

        $this->statement->bindValue($param, $value, $type);
    }

    public function execute(array | null $params = null) : bool
    {
        try
        {
            return $this->statement->execute($params);
        }
        catch (PDOException $e)
        {
            $this->handleError($e);
        }

        return false;
    }

    public function setFetchMode(array $parameters = [PDO::FETCH_ASSOC]) : void
    {
        $this->statement->setFetchMode(...$parameters);
    }

    public function fetch(array | null $params = null) : mixed
    {
        $this->execute($params);

        return $this->statement->fetch();
    }

    public function fetchAll(array | null $params = null) : array
    {
        $this->execute($params);

        return $this->statement->fetchAll();
    }
    
    public function fetchObject(array | null $params = null, string | null $class = null, array $constructArgs = []) : object | false
    {
        $this->execute($params);

        return $this->statement->fetchObject($class, $constructArgs);
    }

    public function rowCount(array | null $params = null) : int
    {
        $this->execute($params);

        return $this->statement->rowCount();
    }

    public function fetchColumn(int $column = 0, array | null $params = null) : mixed
    {
        $this->execute($params);
        
        return $this->statement->fetchColumn($column);
    }

    public function lastInsertId(?string $name = null) : string | false
    {
        return $this->handler->lastInsertId($name);
    }
    
    public function beginTransaction() : bool
    {
        return $this->handler->beginTransaction();
    }
    
    public function commit() : bool
    {
        return $this->handler->commit();
    }
    
    public function rollBack() : bool
    {
        return $this->handler->rollBack();
    }

    public function getErrorInfo() : array
    {
        return $this->handler->errorInfo();
    }

    public function getErrorCode() : string
    {
        return $this->handler->errorCode();
    }

    public function handleError(PDOException $exception) : void
    {
        throw new Exception($exception->getMessage());
    }
}
