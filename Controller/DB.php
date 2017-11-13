<?php

namespace Controller;

class DB {

    private $connection = null;

    /**
     * @return \MySQLi|null
     */
    public function getConnection()
    {
        if (!$this->connection) {

            $this->connection = new \MySQLi(DB_FB_HOST, DB_FB_USER, DB_FB_PASSWORD, DB_FB_NAME);

            if (!$this->connection) {
                printf("Невозможно подключиться к базе данных. Код ошибки: %s\n", mysqli_connect_error());
                exit;
            }
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