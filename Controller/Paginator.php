<?php
namespace Controller;

class Paginator {

    /** @var array*/
    private $sql;

    /** @var \PDO*/
    private $connection;

    /** @var int*/
    private $totalRows;

    /** @var int*/
    private $limit;

    /** @var int*/
    private $rows;

    /** @var int*/
    private $page;

    /** @var int*/
    private $pages;

    /**
     * Paginator constructor.
     * @param \PDO $connection
     * @param array $sql
     */
    public function __construct(\MySqli $connection, array $sql)
    {
        $this->connection = $connection;
        $this->sql = $sql;
    }

    /**
     * @param int $page
     * @param int $limit
     */
    public function paginate($page = 1, $limit = 20)
    {
        $this->page = $page;
        $this->limit = $limit;

        $page = (int) $page - 1;
        if ($page < 0) {
            $page = 0;
        }

        $selectSQL = $this->sql['select_sql'];
        $whereSQL = $this->sql['where_sql'];

        $params=[];
        if (isset($this->sql['params'])) {
            $params =  $this->sql['params'];
        }

        //Get Data
        $sql=$selectSQL;
        if (!empty($whereSQL)) {
            $sql .= ' WHERE ' . $whereSQL;
        }

        $sqlWithLimit = $sql . ' LIMIT ' . $page * $limit . ','. $limit;

        $result = mysqli_query( $this->connection, $sqlWithLimit);

        $this->rows = mysqli_fetch_all($result,MYSQLI_ASSOC);

        // Get Pagination

        $result = mysqli_query($this->connection, 'SELECT COUNT(*) FROM (' . $sql .' ) pagination');


        $this->totalRows = $result->fetch_array();
        $this->totalRows = $this->totalRows[0];

        $this->pages = ceil($this->totalRows / $limit);
    }

    /**
     *
     */
    public function markup()
    {
        $pagination = new \Pagination($this->page, $this->totalRows);
        $pagination->setRPP($this->limit);
        $pagination->setTotalRows($this->totalRows);
        $pagination->setNext('&raquo;');
        $pagination->setPrevious('&laquo;');

        return $pagination->parse();
    }

    /**
     * @return int
     */
    public function getRows()
    {
        return $this->rows;
    }
}