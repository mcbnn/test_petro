<?php
/**
 * Created by PhpStorm.
 * User: mcbnn
 * Date: 13.11.17
 * Time: 14:47
 */

namespace Controller;


class IndexController {

    /**
     * @var ModelFactory|null
     */
    private $factory = null;

    /**
     * @var View|null
     */
    private $view = null;

    /**
     * @var null
     */
    private $query = null;

    /**
     * IndexController constructor.
     */
    public function __construct() {

        $this->factory = new ModelFactory();
        $this->view = new View();

    }

    /**
     * init
     */
    public function init() {

        $parseUrl = parse_url($_SERVER["REQUEST_URI"]);

        if (isset($parseUrl['query'])) {
            $this->query = parse_str($parseUrl['query']);
            parse_str($parseUrl['query'], $this->query);
        }

        switch ($parseUrl['path']) {
            case "/":
                $this->indexAction();
                break;
            case "/one_page/":
                $this->onePageAction();
                break;
            case "/remove_page/":
                $this->removePageAction();
                break;
            case "/add_page/":
                $this->addPageAction();
                break;
            case "/save_page/":
                $this->successSavePageAction();
                break;
            case "/redactor_page/":
                $this->redactorPageAction();
                break;
            default :
                die('Данного роута нет');

        }

    }

    /**
     * Success Save Page
     */
    public function successSavePageAction() {

        $this->view->render('save_page');

    }

    /**
     * redactor Page
     */
    public function redactorPageAction() {

        $pageModel = $this->factory->getModel('page');
        $pageId = (int)$_GET['pageId'];
        if ($pageId == 0) die('Нет Id записи');

        if (!empty($_POST)) {
            $getForm = $pageModel->validatePost();
            if (!$pageModel->errForm) {

                if ($pageModel->updateModel($pageId) == '') {
                    header("Location: /save_page/");
                }
            }
        } else {

            $data = $pageModel->getById($pageId);
            $getForm = $pageModel->getForm($data);
        }

        $this->view->render('redactor_page', ['form' => $getForm,]);

    }

    /**
     * @param null $id
     */
    public function addPageAction($id = null) {

        $pageModel = $this->factory->getModel('page');

        if (!empty($_POST)) {
            $getForm = $pageModel->validatePost();
            if (!$pageModel->errForm) {

                if ($pageModel->saveModel() == '') {
                    header("Location: /save_page/");
                }
            }

        } else {
            $getForm = $pageModel->getForm();
        }

        $this->view->render('add_page', ['form' => $getForm]);
    }

    /**
     * Remove Page
     */
    public function removePageAction() {

        $pageModel = $this->factory->getModel('page');
        $pageId = (int)$this->query['pageId'];

        if ($pageId == 0) die('Нет Id записи');

        if ($pageModel->removeId($pageId) == '') {
            header("Location: /");
        } else {
            die("Возникла проблема при удаление записи");
        }

    }

    /**
     * Action Show One Page
     */
    public function onePageAction() {

        $pageModel = $this->factory->getModel('page');
        $pageId = (int)$this->query['pageId'];

        if ($pageId == 0) die('Нет Id записи');

        $this->view->render('one_page', ['data' => $pageModel->getById($pageId)]);
    }

    /**
     * List Show Pages
     */
    public function indexAction() {

        $pageModel = $this->factory->getModel('page');
        $page = $this->query['page'] ?? 1;

        $this->view->render('list', ['paginator' => $pageModel->getAllPaged($page, 2)]);
    }

}

