<?php

namespace Controller;

class PageModel {

    /**
     * @var DB
     */
    private $db;

    /**
     * @var null
     */
    public $errForm = null;

    /**
     * PageModel constructor.
     * @param DB $db
     */
    public function __construct(DB $db) {
        $this->db = $db;
    }

    /**
     * @param null $data
     * @return array
     */
    public function getForm($data = null) {

        $form = [['field' => 'input', 'data' => ['type' => 'text', 'name' => 'title', 'class' => 'form-control', 'required' => 'required'], 'label' => 'Заголовок',], ['field' => 'textarea', 'data' => ['type' => 'textarea', 'name' => 'body', 'class' => 'form-control', 'required' => 'required'], 'label' => 'Текст',], ['field' => 'input', 'data' => ['type' => 'text', 'class' => 'form-control', 'name' => 'keywords', 'required' => 'required'], 'label' => 'Ключевые слова',], ['field' => 'input', 'data' => ['id' => 'datetime', 'type' => 'datetime', 'class' => 'form-control', 'name' => 'modified', 'required' => 'required'], 'label' => 'Модификация',],];

        if ($data) {
            foreach ($form as &$value) {
                foreach ($data as $key2 => $value2) {
                    if ($value['data']['name'] == $key2) {
                        $value['value'] = $value2;

                        if ($value['data']['type'] == 'datetime-local') {
                            try {
                                $datetime = new \DateTime($value2);
                                $_POST[$value['data']['name']] = $datetime->format('Y-m-d H:i:s');

                            } catch (\Exception $e) {

                                $this->errForm = true;
                                $value['error'] = "Неверный формат даты и времени";
                            }

                        }
                    }
                }
            }
        }

        return $form;

    }

    /**
     * @param $pageId
     * @return string
     */
    public function updateModel($pageId) {

        $connection = $this->db->getConnection();
        $stmt = $connection->prepare("UPDATE pet__page set title = :title, body = :body, keywords = :keywords, modified = :modified WHERE pageId = :pageId");
        $_POST['pageId'] = $pageId;
        $stmt->execute($_POST);

        return $stmt->errorCode();
    }

    /**
     * @return string
     */
    public function saveModel() {

        $connection = $this->db->getConnection();
        $stmt = $connection->prepare("INSERT INTO pet__page (title, body, keywords, modified) VALUES (:title, :body, :keywords, :modified);");
        $stmt->execute($_POST);

        return $stmt->errorCode();
    }

    /**
     * @return array
     */
    public function validatePost() {

        $form = $this->getForm();

        foreach ($form as &$value) {

            foreach ($_POST as $key2 => $value2) {

                if ($value['data']['name'] == $key2) {
                    $value['value'] = $value2;
                    if ($value['data']['required'] == 'required') {
                        if (empty($value2)) {
                            $this->errForm = true;
                            $value['error'] = "Поле должно быть заполнено";
                        }
                    }
                    if ($value['data']['type'] == 'datetime') {

                        try {
                            $datetime = new \DateTime($value2);
                            $_POST[$value['data']['name']] = $datetime->format('Y-m-d H:i:s');

                        } catch (\Exception $e) {

                            $this->errForm = true;
                            $value['error'] = "Неверный формат даты и времени";
                        }

                    }
                }

            }

        }

        return $form;
    }

    /**
     * @param int $page
     * @param int $limit
     * @param array $filters
     * @return Paginator
     */
    public function getAllPaged($page = 1, $limit = 20, $filters = []) {
        $connection = $this->db->getConnection();

        $selectSQL = "
          SELECT * FROM pet__page 
        ";
        $params = [];
        $whereSQL = '';

        foreach ($filters as $key => $flter) {

            if (isset($flter['value']) && $flter['value']) {

                if (empty($whereSQL)) {
                    $and = '';
                } else {
                    $and = ' AND ';
                }

                switch ($flter['type']) {
                    case 'like':

                        $keyParam = "param_" . count($params);
                        $whereSQL .= $and . " " . $key . " LIKE :" . $keyParam;
                        $params[$keyParam] = "%" . $flter['value'] . "%";
                        break;
                    default:

                        $keyParam = "param_" . count($params);
                        $whereSQL .= $and . " " . $key . " = :" . $keyParam;
                        $params[$keyParam] = $flter['value'];
                        break;
                }
            }
        }

        $paginator = new Paginator($connection, ['select_sql' => $selectSQL, 'where_sql' => $whereSQL, 'params' => $params]);

        $paginator->paginate($page, $limit);

        return $paginator;
    }

    /**
     * @param $id
     * @return array|mixed
     */
    public function getById($id) {
        $connection = $this->db->getConnection();

        $stmt = $connection->prepare("SELECT * FROM pet__page WHERE pageId = :pageId ");

        if (!$stmt) {
            return [];
        } else {

            $stmt->bindValue('pageId', $id);
            $stmt->execute();
            return $stmt->fetch();

        }
    }

    /**
     * @param $id
     * @return array|string
     */
    public function removeId($id) {
        $connection = $this->db->getConnection();
        $stmt = $connection->prepare("DELETE FROM pet__page WHERE pageId = :pageId");

        if (!$stmt) {
            return [];
        } else {

            $stmt->bindValue('pageId', $id);
            $stmt->execute();
            return $stmt->errorCode();

        }
    }


}