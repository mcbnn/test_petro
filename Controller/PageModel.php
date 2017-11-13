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
        $result = mysqli_query($connection, "SELECT * FROM pet__page WHERE pageId = $id ");
        $result = $result->fetch_array(MYSQLI_ASSOC);

        if (!$result) {
            return [];
        } else {
            return $result;
        }
    }

    /**
     * @param $pageId
     * @return string
     */
    public function updateModel($pageId) {

        $connection = $this->db->getConnection();
        $stmt = $connection->prepare("UPDATE pet__page set title = ?, body = ?, keywords = ?, modified = ? WHERE pageId = ?");
        $stmt->bind_param('sssss', $_POST['title'], $_POST['body'], $_POST['keywords'], $_POST['modified'], $pageId);
        $stmt->execute();

        return $stmt->error;
    }

    /**
     * @return string
     */
    public function saveModel() {

        $connection = $this->db->getConnection();
        $stmt = $connection->prepare("INSERT INTO pet__page (title, body, keywords, modified) VALUES (?, ?, ?, ?);");
        $stmt->bind_param('ssss', $_POST['title'], $_POST['body'], $_POST['keywords'], $_POST['modified']);
        $stmt->execute();

        return $stmt->error;
    }


    /**
     * @param $id
     * @return array|string
     */
    public function removeId($id) {

        $connection = $this->db->getConnection();
        $stmt = $connection->prepare("DELETE FROM pet__page WHERE pageId = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();

        return $stmt->error;
    }

}