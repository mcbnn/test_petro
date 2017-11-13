<?php

namespace Controller;

class DB {

    private $connection = null;

    /**
     * @return null|\PDO
     */
    public function getConnection()
    {
        if (!$this->connection) {

            $dsn = 'mysql:host=' . DB_FB_HOST . ';dbname=' . DB_FB_NAME . ';charset=' . DB_FB_CHARSET ;

            $this->connection = new \PDO($dsn, DB_FB_USER, DB_FB_PASSWORD, [
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_WARNING
                ]
            );

	        $stmt = $this->connection->query('SET NAMES utf8');
	        $stmt->execute();
        }

        return $this->connection;

    }

    /**
     * @param $sql
     * @return array
     */
    public function getAll($sql)
    {
        $stmt = $this->query($sql);

        return $stmt->fetchAll();
    }

    /**
     * @param $sql
     * @return mixed
     */
    public function getOne($sql)
    {
        $stmt = $this->query($sql);

        return $stmt->fetch();
    }

    /**
     * @param $sql
     * @return array|\PDOStatement
     */
    public function query($sql)
    {
        $connection = $this->getConnection();

        $stmt = $connection->query($sql);

        if (!$stmt instanceof \PDOStatement) {

            return [];
        }

        $stmt->execute();


        return  $stmt;
    }
}