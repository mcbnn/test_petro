<?php
namespace Controller;

class ModelFactory {

    /**
     * @var null
     */
    private $db = null;

    /**
     * @param $name
     * @return PageModel|null
     */
    public function getModel($name)
    {
        $model = null;

        switch($name) {

            case 'page':

                $model = new PageModel($this->getDb());
                break;
        }

        return $model;
    }

    /**
     * @return DB|null
     */
    private function getDb()
    {
        if(!$this->db) {
            $this->db = new DB();
        }
        return $this->db;
    }

}