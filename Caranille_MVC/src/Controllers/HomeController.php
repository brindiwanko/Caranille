<?php

require_once(__DIR__ . '/../Models/News.php');

class HomeController {
    public function index() {
        $newsModel = new News();
        $news = $newsModel->getAllNews();

        require_once(__DIR__ . '/../Views/home.php');
    }
}
